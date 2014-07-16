<?php
require '../vendor/autoload.php';

$app = new \Slim\Slim(array(
    'templates.path' => 'templates'
));

$app->get('/', function () use ($app) {
    $app->render('main.php');
});

$app->get('/angular', function () use ($app) {
    $app->render('angular.php');
});

$app->get('/run_test', function () use ($app) {
    $app->response->setStatus(200);
    //send back the pid
    $app->response->setBody(33);
});


$app->run();