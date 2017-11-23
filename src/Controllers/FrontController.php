<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZWorkshop\Models\ImageModel;
use ZWorkshop\Models\ProfileModel;

/**
 * The front controller.
 */
class FrontController extends BaseController
{
    /**
     * The index action.
     *
     * @param Application $app
     *
     * @return RedirectResponse
     */
    public function index(Application $app): RedirectResponse
    {
        return $app->redirect('/login');
    }

    /**
     * The profile action.
     *
     * @param Application $app
     * @param string      $username
     *
     * @return Response
     */
    public function profile(Application $app, string $username): Response
    {
        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];

        $profileModel = new ProfileModel($dbConnection);
        $userData = $profileModel->get($username);

        // If user does not exist return 404.
        if (!$userData){
            $app->abort(Response::HTTP_NOT_FOUND, sprintf('Username %s does not exist.', $username));
        }

        $imageModel = new ImageModel($dbConnection);
        $userImages = $imageModel->getUserCollection($username);

        return $this->render($app, 'profile.html.twig', [
            'title' => 'Profile Page',
            'username' => $userData['Username'],
            'firstName' => $userData['FirstName'],
            'lastName' => $userData['LastName'],
            'images' => $userImages,
        ]);
    }

    /**
     * The login action.
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return Response
     */
    public function login(Application $app, Request $request): Response
    {
        // If user is logged in, redirect to admin page.
        if ($app['security.token_storage']->getToken()) {
            return $app->redirect('/admin');
        }

        return $this->render($app, 'login.html.twig', [
            'title' => 'Login',
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ]);
    }
}
