<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZWorkshop\Models\ImageModel;
use ZWorkshop\Models\ProfileModel;
use ZWorkshop\Services\EmotionService;

/**
 * The admin controller.
 */
class AdminController extends BaseController
{
    private const IMAGE_UPLOAD_DIR =  __DIR__.'/../../web/images/';

    /**
     * The homepage.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function index(Application $app, Request $request): Response
    {
        $username = $app['security.token_storage']->getToken()->getUser();

        // Get user details.
        $profileModel = new ProfileModel($app['pdo.connection']);
        $userDetails = $profileModel->get($username);

        // Get user images.
        $imageModel = new ImageModel($app['pdo.connection']);
        $userImages = $imageModel->getUserCollection($username);

        // Get message from session and delete it afterwards.
        $session = $request->getSession();
        $message = $session->get('message');
        $session->remove('message');

        return $this->render($app, 'admin.html.twig', [
            'title' => 'Admin Panel',
            'username' => $username,
            'firstName' => $userDetails['FirstName'],
            'lastName' => $userDetails['LastName'],
            'email' => $userDetails['Email'],
            'gender' => $userDetails['Gender'],
            'programmingLanguages' => explode('|', $userDetails['ProgramingLanguages']),
            'description' => $userDetails['Description'],
            'images' => $userImages,
            'message' => $message,
        ]);
    }

    /**
     * The save profile action.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return RedirectResponse
     */
    public function saveProfile(Application $app, Request $request): RedirectResponse
    {
        $username = $app['security.token_storage']->getToken()->getUser();
        if (Request::METHOD_POST === $request->getMethod()) {
            // Get params from post request.
            $firstName = $request->get('first_name');
            $lastName = $request->get('last_name');
            $email = $request->get('email');
            $gender = $request->get('gender');
            $userDescription = $request->get('user_description');
            $programmingLanguages = implode('|', $request->get('programming_languages'));

            $profileModel = new ProfileModel($app['pdo.connection']);
            try {
                $result = $profileModel->save(
                    $username,
                    $firstName,
                    $lastName,
                    $email,
                    $gender,
                    $programmingLanguages,
                    $userDescription
                );

                // Check for updated rows.
                $message = $result ? 'Successfully updated user data!' : 'User data was not changed!';
            } catch (\Throwable $e) {
                $message = 'An error occurred, the data was not updated! ';
            }
        }

        // Redirect with a message.
        $redirectUrl = '/admin';
        if (!empty($message)) {
            $request->getSession()->set('message', $message);
        }

        return $app->redirect($redirectUrl);
    }

    /**
     * The save image action.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return RedirectResponse
     */
    public function saveImage(Application $app, Request $request): RedirectResponse
    {
        $username = $app['security.token_storage']->getToken()->getUser()->getUsername();
        $profileModel = new ProfileModel($app['pdo.connection']);
        $profile = $profileModel->get($username);

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (is_null($file)) {
            $message = 'No file uploaded!';
        } else {
            $filename = uniqid('', true).'.'.$file->getClientOriginalExtension();
            try {
                $file->move(self::IMAGE_UPLOAD_DIR, $filename);

                $imageUrl = self::IMAGE_UPLOAD_DIR.DIRECTORY_SEPARATOR.$filename;

                $emotionApi = new EmotionService($app);
                $emotions = json_encode($emotionApi->analyze($imageUrl));

                $imageModel = new ImageModel($app['pdo.connection']);
                $imageModel->save($profile['IdUser'], $filename, $emotions);

                $message = 'File was successfully uploaded!';
            } catch (FileException $e) {
                $message = 'File uploaded, but could not be moved!';
            }
        }

        // Redirect with a message.
        $redirectUrl = '/admin';
        $request->getSession()->set('message', $message);

        return $app->redirect($redirectUrl);
    }

    /**
     * The delete image action.
     *
     * @param Application $app
     * @param Request     $request
     * @param int         $imageId
     *
     * @return RedirectResponse
     */
    public function deleteImage(Application $app, Request $request, int $imageId): RedirectResponse
    {
        $imageModel = new ImageModel($app['pdo.connection']);
        $image = $imageModel->get($imageId);

        $filePath = self::IMAGE_UPLOAD_DIR.DIRECTORY_SEPARATOR.$image['FileName'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $message = 'Successfully deleted image!';
        if (!$imageModel->delete($imageId)) {
            $message = 'An error occurred, the image was not deleted.';
        }

        // Redirect with a message.
        $redirectUrl = '/admin';
        $request->getSession()->set('message', $message);

        return $app->redirect($redirectUrl);
    }
}
