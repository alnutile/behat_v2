<?php
require '../vendor/autoload.php';

use BehatEditor\BehatEditorApp;
use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatWrapper\BehatCommand;
use BehatEditor\Tests\BaseTest;
use BehatWrapper\BehatWrapper;
use BehatEditor\BehatOutputListener;


$app = new \Slim\Slim(array(
    'templates.path' => 'templates'
));

$app->get('/', function () use ($app) {
    $app->render('main.php');
});

$app->get('/angular', function () use ($app) {
    $app->render('angular.php');
});
$app->get('/run_fail/:repo_setting_id/filename/:filename', function ($repo_setting_id, $filename) use ($app) {

    //1. Query for the repo info
    //@TODO add a repo_query_step

    //2. then pass this along

    //Other Params could be
    //1. Use Token 1/0
    //2. Token Filename relative to above
    $params = $app->request->get();

    $behatApp = new BehatEditorApp();
    $response = $behatApp->run($repo_setting_id, $filename);
    $app->response->setBody($response);

});

$app->get('/run/:repo_setting_id/filename/:filename', function ($repo_setting_id, $filename) use ($app) {

    //1. Query for the repo info
    //@TODO add a repo_query_step

    //2. then pass this along

    //Other Params could be
    //1. Use Token 1/0
    //2. Token Filename relative to above
    $params = $app->request->get();

    $behatApp = new BehatEditorApp();
    $response = $behatApp->run($repo_setting_id, 'test_wiki.feature');
    $app->response->setBody($response);

});

$app->get('/run_test', function () use ($app) {

    $behat_wrapper = new BehatWrapper();
    $bin = __DIR__ . '/../bin/';
    $yaml = __DIR__ . '/../private/behat.yml';
    $test = __DIR__ . '/../private/features/wikipedia.feature';

    $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

    //Set Stream Output
    //without this the output would not come till the end
    //

    $behat_wrapper->streamOutput(false);

    //Add behat.command.prepare
    $setName = new BehatSetNewNameOnYaml();
    $listener = new BehatPrepareListener($setName);

    $behat_wrapper->addPrepareListener($listener);

    //This one gets Output while it is going line by line
    $outputListener = new BehatOutputListener();
    $behat_wrapper->addOutputListener($outputListener);

    $command = BehatCommand::getInstance()
        ->setOption('config', $yaml)
        ->setOption('profile', 'phantom')
        ->setTestPath($test);

    try {
        $output = $behat_wrapper->run($command);
        $app->response->setStatus(200);
        $app->response->setBody($output);
    }
    catch(\Exception $e){
        $app->response->setStatus(200);
        $app->response->setBody($e->getMessage());
    }

});

$app->get('/stream_process', function() use ($app) {
    $app->response->setStatus(200);
    $app->response->setBody("Running");
});

$app->run();