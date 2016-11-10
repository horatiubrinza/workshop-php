<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

        $sql = "SELECT `FirstName`, `LastName`, `Username` FROM `users` WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);
        $userData = $query->fetch();

        // if user not exists return 404
        if($userData['Username'] != $username){
            return  $app->abort(404, "Username $username does not exist.");
        }

        // user exists get images
        $sql = "SELECT `images`.IdImage, `images`.FilePath, `images`.ProcessingResut FROM `users` 
                JOIN `images` USING(IdUser)
                WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);
        $userImages = $query->fetchAll();

        //dump($userImages);die;

        return $app['twig']->render('profile.html.twig', array(
            'title' => 'Profile Page',
            'username' => $userData['Username'],
            'firstName' => $userData['FirstName'],
            'lastName' => $userData['LastName'],
            'images' => $userImages,
        ));
    }
}
