<?php

use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\Bootstrap;
use ZWorkshop\Services\EmotionAPI;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Initialize Aplication
 */
$app = new Silex\Application();

/**
 * Register services
 */
Bootstrap::init($app);

/**
 * Define root route
 */
$app->get('/', 'ZWorkshop\\Controllers\\FrontController::index');

/**
* Define profile route
*/
$app->get('/profile/{username}', 'ZWorkshop\\Controllers\\FrontController::profile');

/**
 * Define login route
 */
$app->get('/login/', 'ZWorkshop\\Controllers\\FrontController::login');

/**
 * Define admin route
 */
$app->get('/admin/', 'ZWorkshop\\Controllers\\AdminController::index');

/**
 * Define save-profile rute
 */
$app->post('/admin/save-profile', 'ZWorkshop\\Controllers\\AdminController::saveProfile');

/**
 * Define save-image route
 */
$app->post('/admin/save-image', 'ZWorkshop\\Controllers\\AdminController::saveImage');

/**
 * Define delete-image route
 */
$app->get('/admin/delete-image/{imageId}', 'ZWorkshop\\Controllers\\AdminController::deleteImage');

$app->run();
