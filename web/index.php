<?php

use Symfony\Component\HttpFoundation\Request;
use ZWorkshop\EmotionAPI;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../src/views',
));

$app['debug'] = true;

$app->get('/test-emotion-api', function(Request $request) {

    $emotionApi = new EmotionAPI();
    $imageUrl = 'D:/projects/workshop/source/upload/demo-emotions.jpg';

    $emotions = $emotionApi->analyze($imageUrl);
    dump($emotions);
    return '';
});


$app->get('/admin', function() use ($app){
    return $app['twig']->render('admin.html.twig', array(
        'title' => 'Admin Panel',
    ));
});

$app->run();
