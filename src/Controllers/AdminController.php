<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
        return $app['twig']->render('login.html.twig', array(
            'title' => 'Login',
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
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
        $dbConnection = $app['pdo.connection'];
        $username = $app['security.token_storage']->getToken()->getUser();
        $message = $request->get('message');

        // get user details
        $sql = "SELECT * FROM `users` WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);
        $userDetails = $query->fetch();

        // get user images
        $sql = "SELECT `images`.IdImage, `images`.FilePath, `images`.ProcessingResut FROM `users` 
                JOIN `images` USING(IdUser)
                WHERE `username` = :username;";
        $params = [
            ':username' => $username,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);
        $userImages = $query->fetchAll();

        return $app['twig']->render('admin.html.twig', array(
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
        ));
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

    /**
     * Save image route
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveImage(Application $app, Request $request)
    {

        // define upload dir
        $fileUploadPath = __DIR__ . '/../../upload/';

        // upload file
        $file = $request->files->get('file');
        $filename = $file->getClientOriginalName();
        $file->move($fileUploadPath, $filename);

        /**
         * TODO: call api and process the image
         */
        $this->saveImageToDatabase($app, '1', 'test.jpg', 'test json');

        // redirect with a message
        $message = 'File was successfully uploaded!';
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }

    /**
     * After processing save image data to database
     *
     * @param Application $app
     * @param $idUser
     * @param $filePath
     * @param $processingResult
     * @return int
     */
    private function saveImageToDatabase(Application $app, $idUser, $filePath, $processingResult)
    {

        /** @var \PDO $dbConnection */
        $dbConnection = $app['pdo.connection'];

        $sql = "INSERT INTO `images` (`IdUser`, `FilePath`, `ProcessingResut`)
                VALUES (:idUser, :filePath, :processingResult)";

        $params = [
            ':idUser' => $idUser,
            ':filePath' => $filePath,
            ':processingResult' => $processingResult,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);

        return $query->rowCount();
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

        // get user details
        $sql = "DELETE FROM `images` WHERE `IdImage` = :imageId;";
        $params = [
            ':imageId' => $imageId,
        ];

        $query = $dbConnection->prepare($sql);
        $query->execute($params);

        $message = 'Successfully deleted Image!';
        if (!$query->rowCount()) {
            $message = 'An error occuried the image was not deleted.';
        }

        // redirect with a message
        $redirectUrl = '/admin?message=' . $message;

        return $app->redirect($redirectUrl);
    }
}
