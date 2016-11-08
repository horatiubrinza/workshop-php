<?php

use IPC\Silex\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Services\EmotionAPI;
use ZWorkshop\Services\UserProvider;
use ZWorkshop\Services\PasswordEncoderService;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

/**
 * Register components.
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/views',
));

$app->register(new PDOServiceProvider(), [
    'pdo.options' => [
        'dsn' => 'mysql:host=localhost;dbname=php_workshop;charset=UTF8',
        'username' => 'root',
        'password' => 'root',
        'options' => [],
        'attributes' => [],
    ],
]);

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'logout' => array('logout_path' => '/admin/logout', 'invalidate_session' => true),
            'users' => function () use ($app) {
                return new UserProvider($app['pdo.connection']);
            },
        )
    ),
    'security.default_encoder' => function () {
        // Plain text (e.g. for debugging)
        return new PasswordEncoderService();
    },
));

$app->register(new Silex\Provider\SessionServiceProvider());


$app['debug'] = true;

/**
 * Test emotion api route
 */
$app->get('/test-emotion-api', function (Request $request) {

    $emotionApi = new EmotionAPI();
    $imageUrl = 'D:\programs\wamp64\www\workshop-php-a-zitec\upload\demo-emotions.jpg';

    $emotions = $emotionApi->analyze($imageUrl);
    dump($emotions);
    return '';
});


$app->get('/', function (Request $request) use ($app) {

    return $app->redirect('admin');

});

/**
 * Define login route
 */
$app->get('/login', function (Request $request) use ($app) {

    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));

});


/**
 * Define admin route
 */
$app->get('/admin', function (Request $request) use ($app) {

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

});


/**
 * Define save-user-data route
 */
$app->post('/save-user-data', function (Request $request) use ($app) {

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
    $redirectUrl = 'admin?message=' . $message;

    return $app->redirect($redirectUrl);
});


/**
 * Define process-image route
 */
$app->post('/process-image', function (Request $request) use ($app){

    // define upload dir
    $fileUploadPath = __DIR__.'/../upload/';

    // upload file
    $file = $request->files->get('file');
    $filename = $file->getClientOriginalName();
    $file->move($fileUploadPath,$filename);

    /**
     * TODO: call api and process the image
     */

    // redirect with a message
    $message = 'File was successfully uploaded!';
    $redirectUrl = 'admin?message=' . $message;

    return $app->redirect($redirectUrl);

});

$app->run();
