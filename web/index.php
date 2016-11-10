<?php

use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Bootstrap;
use ZWorkshop\Services\EmotionAPI;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

/**
 * Register components.
 */

Bootstrap::init($app);

/**
 * Define root route
 */
$app->get('/', 'ZWorkshop\\Controllers\\FrontController::index');

/**
* Define profile route
*/
$app->get('/profile', 'ZWorkshop\\Controllers\\FrontController::profile');

/**
 * Define login route
 */
$app->get('/login', 'ZWorkshop\\Controllers\\AdminController::login');

/**
 * Define admin route
 */
$app->get('/admin', 'ZWorkshop\\Controllers\\AdminController::index');

/**
 * Define save-user-data route
 */
$app->post('/admin/save-user-data', 'ZWorkshop\\Controllers\\AdminController::saveProfile');



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
