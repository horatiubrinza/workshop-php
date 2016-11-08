<?php

use IPC\Silex\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use ZWorkshop\Services\EmotionAPI;
use ZWorkshop\Services\UserProvider;

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


/**
 * Admin route
 */
$app->get('/admin', function () use ($app) {
    return $app['twig']->render('admin.html.twig', array(
        'title' => 'Admin Panel',
    ));
});


/**
 * Save user data route
 */
$app->post('/save-user-data', function (Request $request) use ($app) {

    /** @var \PDO $dbConnection */
    $dbConnection = $app['pdo.connection'];

    if (isset($request->request)) {

        $userName = 'kristo.godari';

        $firstName = $request->request->get('frist_name');
        $lastName = $request->request->get('last_name');
        $email = $request->request->get('email');
        $gender = $request->request->get('gender');
        $userDescription = $request->request->get('user_description');
        $programmingLanguages = implode('|', $request->request->get('programming_languages'));


        $sql = "UPDATE `users` 
                SET `FirstName`= :firstName, 
                      `LastName` = :lastName, 
                      `Email` = :email, 
                      `Gender` = :gender, 
                      `ProgramingLanguages` = :programmingLanguages, 
                      `Description` =  :userDescription
                WHERE `username` = :username;";

        $params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':gender' => $gender,
            ':programmingLanguages' => $programmingLanguages,
            ':userDescription' => $userDescription,
            ':username' => $userName,
        ];

        try {

            $query = $dbConnection->prepare($sql);
            $query->execute($params);

            $redirectUrl = 'admin?success=0';
            if($query->rowCount()){
                $redirectUrl = 'admin?success=1';
            }

        }catch(Exception $e) {
            $redirectUrl = 'admin?success=0';
        }
    }

    return $app->redirect($redirectUrl);
});


/**
 * Process Image route
 */
$app->post('/process-image', function (Request $request) {
    /*
     * Do something with the image
     */
    dump($request);
});

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
        return new PlaintextPasswordEncoder();
    },
));

$app->register(new Silex\Provider\SessionServiceProvider());
//$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->run();
