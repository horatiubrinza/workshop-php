<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Models\ImageModel;
use ZWorkshop\Models\ProfileModel;

class AdminController
{
    /**
     * Login route
     *
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function login(Application $app, Request $request)
    {
        return $app['twig']->render('login.html.twig', [
            'title'         => 'Login',
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ]);
    }

    /**
     * Index route
     *
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function index(Application $app, Request $request)
    {
        /** @var \PDO $dbConnection */
        $username = $app['security.token_storage']->getToken()->getUser();
        $message = $request->get('message');

        // get user details
        $profileModel = new ProfileModel($app['pdo.connection']);
        $userDetails = $profileModel->get($username);

        // get user images
        $imageModel = new ImageModel($app['pdo.connection']);
        $userImages = $imageModel->getUserImages($username);

        return $app['twig']->render('admin.html.twig', [
            'title'                => 'Admin Panel',
            'username'             => $username,
            'firstName'            => $userDetails['FirstName'],
            'lastName'             => $userDetails['LastName'],
            'email'                => $userDetails['Email'],
            'gender'               => $userDetails['Gender'],
            'programmingLanguages' => explode('|', $userDetails['ProgramingLanguages']),
            'description'          => $userDetails['Description'],
            'images'               => $userImages,
            'message'              => $message,
        ]);
    }

    /**
     * Save profile route
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveProfile(Application $app, Request $request)
    {

        /** @var \PDO $dbConnection */
        $username = $app['security.token_storage']->getToken()->getUser();

        if (isset($request->request)) {

            // get params form post request
            $firstName = $request->get('frist_name');
            $lastName = $request->get('last_name');
            $email = $request->get('email');
            $gender = $request->get('gender');
            $userDescription = $request->get('user_description');
            $programmingLanguages = implode('|', $request->get('programming_languages'));

            $profileModel = new ProfileModel($app['pdo.connection']);

            try {
                $result = $profileModel->save($username, $firstName, $lastName, $email, $gender, $programmingLanguages,
                    $userDescription);

                // check for updated rows
                $message = $result ? 'Successfully updated user data!' : 'User data was not changed!';

            } catch (\Exception $e) {
                $message = 'An error occurred, the data was not updated! ';
            }
        }

        // redirect with a message
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }

    /**
     * Save image route
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveImage(Application $app, Request $request)
    {
        $dbConnection = $app['pdo.connection'];

        $username = $app['security.token_storage']->getToken()->getUser();
        $profileModel = new ProfileModel($dbConnection);
        $profile = $profileModel->get($username);

        // define upload dir
        $fileUploadDir = __DIR__ . '/../../upload/';

        // upload file
        $file = $request->files->get('file');
        if (is_null($file)) {
            $message = 'No file uploaded!';
        } else {
            $filename = $file->getClientOriginalName();

            if (!$file->move($fileUploadDir, $filename)) {
                $message = 'File uploaded, bit could not be moved!';
            } else {
                $filePath = $fileUploadDir . DIRECTORY_SEPARATOR . $filename;
                /**
                 * TODO: call api and process the image
                 */
                $imageModel = new ImageModel($dbConnection);
                $imageModel->saveImage($profile['IdUser'], $filePath, 'test json');
                $message = 'File was successfully uploaded!';
            }
        }

        // redirect with a message
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }

    /**
     * Delete image route
     *
     * @param Application $app
     * @param Request $request
     * @param $imageId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteImage(Application $app, Request $request, $imageId)
    {

        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];

        $imageModel = new ImageModel($dbConnection);

        $message = 'Successfully deleted image!';
        if (!$imageModel->deleteImage($imageId)) {
            $message = 'An error occurred, the image was not deleted.';
        }

        //TODO: delete image file

        // redirect with a message
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }
}
