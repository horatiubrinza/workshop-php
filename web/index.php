<?php

use ZWorkshop\EmotionAPI;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/test-emotion-api', function() {
    $emotionApi = new EmotionAPI();
    $imageUrl = 'D:/projects/workshop/source/upload/demo-emotions.jpg';

    return $emotionApi->analyze($imageUrl);
});

$app->run();
