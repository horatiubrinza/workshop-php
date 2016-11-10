<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Models\ImageModel;
use ZWorkshop\Models\ProfileModel;

class FrontController
{
    /**
     * Index route
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app)
    {
        return $app->redirect('profile');
    }

    /**
     * Profile route
     *
     * @param Application $app
     * @param Request $request
     * @param $username
     */
    public function profile(Application $app, Request $request, $username)
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
        $userImages = $imageModel->getUserImages($username);

        return $app['twig']->render('profile.html.twig', array(
            'title' => 'Profile Page',
            'username' => $userData['Username'],
            'firstName' => $userData['FirstName'],
            'lastName' => $userData['LastName'],
            'images' => $userImages,
        ));
    }
}
