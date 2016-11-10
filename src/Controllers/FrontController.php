<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Models\ImageModel;
use ZWorkshop\Models\ProfileModel;

class FrontController
{
    /**
     * Index action
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app)
    {
        return $app->redirect('profile');
    }

    /**
     * Profile action
     *
     * @param Application $app
     * @param $username
     */
    public function profile(Application $app, $username)
    {
        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];

        $profileModel = new ProfileModel($dbConnection);
        $userData = $profileModel->get($username);

        // if user does not exist return 404
        if(!$userData){
            $app->abort(404, "Username $username does not exist.");
        }

        $imageModel = new ImageModel($dbConnection);
        $userImages = $imageModel->getUserCollection($username);

        return $app['twig']->render('profile.html.twig', array(
            'title' => 'Profile Page',
            'username' => $userData['Username'],
            'firstName' => $userData['FirstName'],
            'lastName' => $userData['LastName'],
            'images' => $userImages,
        ));
    }

    /**
     * Login action
     *
     * @param Application $app
     * @param Request $request
     * @return mixed
     */
    public function login(Application $app, Request $request)
    {
        $token = $app['security.token_storage']->getToken();
        if ($token) {
            //user is logged in, go to admin page
            return $app->redirect('/admin');
        }

        return $app['twig']->render('login.html.twig', [
            'title'         => 'Login',
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ]);
    }
}
