<?php

namespace BehatEditor\Core\Tests;

use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatEditor\Tests\Base;
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


class BehatEditorTest extends Base {


    /**
     * @expectedException \BehatWrapper\BehatException
     *
     * @test
     */
    public function thow_exception_on_test_fail()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/fail_test.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        $behat_wrapper->run($command);
    }



    /**
     * @test
     */
    public function listen_on_wrapper_non_output_event()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/local.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners

        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        //If the test fails this errors out!
        // not output at this point

        $output = $behat_wrapper->run($command);

        $this->assertNotNull($output);
    }

    /**
     * @test
     */
    public function listen_on_wrapper_phantom_prepare_event_set_tmp_folder()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/local.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners

        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        //Add behat.command.prepare
        $setName = new BehatSetNewNameOnYaml();
        $setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        //@TODO how to much run the behat side of this for testing
        $behat_wrapper->run($command);
        $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
    }

    /**
     * @test
     */
    public function listen_on_wrapper_phantom_prepare_event_set_name_now_in_yaml_file()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/local.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners

        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        //Add behat.command.prepare

        $setName = new BehatSetNewNameOnYaml();
        $setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        //@TODO how to much run the behat side of this for testing

        $behat_wrapper->run($command);

        $this->assertContains($setName->getYmlName(), file_get_contents($command->getOptions()['config']));

    }



    /**
     * Test Get Sl Job
     * this was not needed since that API works and is tested. Using the uuid name show above to work means this will work
     */

    /**
     * Test Get Output on Fail
     * need to still get output on a fail
     *
     * @test
     */
    public function get_output_on_failed_test()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/fail_test.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Set Stream Output
        //without this the output would not come till the end
        //
        $behat_wrapper->streamOutput(true);

        //This one gets Output while it is going line by line
        $outputListener = new BehatOutputListener();
        $behat_wrapper->addOutputListener($outputListener);

        //Add behat.command.prepare
        $setName = new BehatSetNewNameOnYaml();
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        try {
            $behat_wrapper->run($command);
        }
        catch(\BehatWrapper\BehatException $e) {

        }
        //At this point we have
        // the name
        // the output of the test
        // and we know if it is pass or fail via the catch above
        //
        var_dump($outputListener->getOutput());
        $this->assertNotEmpty($outputListener->getOutput());
    }

    /**
     * Leave buffer open till test is done
     *
     * @test
     */
    public function set_stream_true_for_output()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../../bin/';
        $yaml = __DIR__ . '/../../../../private/behat.yml';
        $test = __DIR__ . '/../../../../private/features/local.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Set Stream Output
        //without this the output would not come till the end
        //
        $behat_wrapper->streamOutput(true);

        //This one gets Output while it is going line by line
        $outputListener = new BehatOutputListener();
        $behat_wrapper->addOutputListener($outputListener);

        //Add behat.command.prepare
        $setName = new BehatSetNewNameOnYaml();
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'phantom')
            ->setTestPath($test);

        try {
            $behat_wrapper->run($command);
        }
        catch(\BehatWrapper\BehatException $e) {

        }
        //At this point we have
        // the name
        // the output of the test
        // and we know if it is pass or fail via the catch above
        //
        $this->assertNotEmpty($outputListener->getOutput());
    }


//**
// Test below here are not best to run
//**

//    /**
//     * Using a background Process
//     * Seems to have issues in PhpUnit not outside of it
//     *
//     * @test
//     */
//    public function using_background_process_and_checking_in()
//    {
//        $process = new Process('sleep(10)');
//        $process->start();
//        var_dump("It is running for 10 seconds");
//        while($process->isRunning())
//        {
//            foreach(range(1,10) as $index){
//                echo "$index \n";
//                sleep(1);
//            }
//        }
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../../bin/';
//        $yaml = __DIR__ . '/../../../../private/behat.yml';
//        $test = __DIR__ . '/../../../../private/features/local_long_test.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Set Stream Output
//        //without this the output would not come till the end
//        //
//        $behat_wrapper->streamOutput(false);
//
//        //Add behat.command.prepare
//        $setName = new BehatSetNewNameOnYaml();
//        $listener = new BehatPrepareListener($setName);
//
//        $behat_wrapper->addPrepareListener($listener);
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//
//        //Run and Release
//        $process = $behat_wrapper->start($command);
//        var_dump("This is running now for output");
//
//        //Get output
//        while($process->isRunning()) {
//            var_dump("Is running " . date('U'));
//            var_dump($process->getPid());
//            sleep(1);
//        }
//    }


    /**
     * Test Reporting API
     * get on output and uuid to set the report and the remote_report tables together belongsTo type relationship
     */

} 