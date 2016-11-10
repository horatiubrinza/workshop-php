<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AdminController
{
    public function login(Application $app, Request $request)
    {
        return $app['twig']->render('login.html.twig', array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

    public function index(Application $app, Request $request)
    {
        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];
        $username = $app['security.token_storage']->getToken()->getUser();

        $sql = "SELECT * FROM `users` WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);
        $results = $query->fetch();

        $message = $request->get('message');

        return $app['twig']->render('admin.html.twig', array(
            'title' => 'Admin Panel',
            'firstName' => $results['FirstName'],
            'lastName' =>$results['LastName'],
            'email' => $results['Email'],
            'gender' => $results['Gender'],
            'programmingLanguages' => explode('|', $results['ProgramingLanguages']),
            'description' => $results['Description'],
            'message' => $message,
        ));
    }

    public function saveProfile(Application $app, Request $request) {

        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];
        $username = $app['security.token_storage']->getToken()->getUser();

        if (isset($request->request)) {

            // get params form post request
            $firstName = $request->request->get('frist_name');
            $lastName = $request->request->get('last_name');
            $email = $request->request->get('email');
            $gender = $request->request->get('gender');
            $userDescription = $request->request->get('user_description');
            $programmingLanguages = implode('|', $request->request->get('programming_languages'));


            // define query
            $sql = "UPDATE `users`
                SET `FirstName`= :firstName,
                      `LastName` = :lastName,
                      `Email` = :email,
                      `Gender` = :gender,
                      `ProgramingLanguages` = :programmingLanguages,
                      `Description` =  :userDescription
                WHERE `username` = :username;";

            // define query params
            $params = [
                ':firstName' => $firstName,
                ':lastName' => $lastName,
                ':email' => $email,
                ':gender' => $gender,
                ':programmingLanguages' => $programmingLanguages,
                ':userDescription' => $userDescription,
                ':username' => $username,
            ];

            try {

                // bind params to query and execute query
                $query = $dbConnection->prepare($sql);
                $query->execute($params);

                // check for updated rows
                $message = 'Successfully updated user data!';
                if ($query->rowCount()) {
                    $message = 'User data was not updated!';
                }

            } catch (Exception $e) {
                $message = 'An error occured the data was not updated! ' . $e;
            }
        }

        // redirect with a message
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }

    public function saveImage(Application $app, Request $request) {

        // define upload dir
        $fileUploadPath = __DIR__.'/../../upload/';

        // upload file
        $file = $request->files->get('file');
        $filename = $file->getClientOriginalName();
        $file->move($fileUploadPath,$filename);

        /**
         * TODO: call api and process the image
         */

        // redirect with a message
        $message = 'File was successfully uploaded!';
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }
}
