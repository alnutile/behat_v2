<?php

require(__DIR__.'/vendor/autoload.php');

use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatWrapper\BehatCommand;
use BehatEditor\Tests\BaseTest;
use BehatWrapper\BehatWrapper;
use BehatWrapper\Event\BehatEvent;
use BehatWrapper\Event\BehatEvents;
use BehatWrapper\Event\BehatPrepareEvent;
use BehatWrapper\Event\BehatOutputEvent;
use BehatWrapper\Event\BehatOutputListenerInterface;
use BehatWrapper\Event\BehatPrepareListenerInterface;
use Symfony\Component\Filesystem\Filesystem;
use BehatEditor\BehatYmlParser;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use BehatEditor\BehatOutputListener;

//$process = new Process('sleep(10)');
//$process->start();
//var_dump("It is running for 10 seconds");
//while($process->isRunning())
//{
//    foreach(range(1,10) as $index){
//        echo "$index \n";
//        sleep(1);
//    }
//}

$behat_wrapper = new BehatWrapper();
$bin = __DIR__ . '/bin/';
$yaml = __DIR__ . '/private/behat.yml';
$test = __DIR__ . '/private/features/wikipedia_long.feature';

$behat_wrapper->setBehatBinary($bin)->setTimeout(600);

//Set Stream Output
//without this the output would not come till the end
//
$behat_wrapper->streamOutput(false);

//Add behat.command.prepare
$setName = new BehatSetNewNameOnYaml();
$listener = new BehatPrepareListener($setName);

$behat_wrapper->addPrepareListener($listener);

$command = BehatCommand::getInstance()
    ->setOption('config', $yaml)
    ->setOption('profile', 'saucelabs')
    ->setTestPath($test);

//Run and Release
$process = $behat_wrapper->start($command);
var_dump("This is running now for output of pid " . $process->getPid());

//Get output but find the process based on the pid
while($process->isRunning()) {
    echo "Is running " . @date('U') . "\n";
    echo $process->getPid();
    echo $behat_wrapper->getOutput($process);
    sleep(1);
}