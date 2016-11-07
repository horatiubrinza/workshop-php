<?php

use IPC\Silex\Provider\PDOServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\EmotionAPI;

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
        'password' => '',
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

    $dbConnection = $app['pdo.connection'];

    if (isset($request->request)) {

        $userName = 'kristo.godari';

        $firstName = $request->request->get('frist_name');
        $lastName = $request->request->get('last_name');
        $email = $request->request->get('email');
        $sex = $request->request->get('sex');
        $userDescription = $request->request->get('user_description');
        $programmingLanguages = implode('|', $request->request->get('programming_languages'));


        $sql = "UPDATE `users` SET (`FirstName`, `LastName`, `Email`, `Sex`, `ProgramingLanguages`, `Description`)
                  VALUES (:firstName, :lastName, :email, :sex, :programmingLanguages, :userDescription)
                  WHERE `username` = :username;";

        $params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':sex' => $sex,
            ':programmingLanguages' => $programmingLanguages,
            ':userDescription' => $userDescription,
            ':username' => $userName,
        ];

        try {

            $query = $dbConnection->prepare($sql);
            $query->execute(array(
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'sex' => $sex,
                'programmingLanguages' => $programmingLanguages,
                'userDescription' => $userDescription,
                'username' => $userName,
            ));

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

$app->run();
