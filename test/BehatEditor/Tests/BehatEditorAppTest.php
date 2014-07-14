<?php

namespace BehatEditor\Tests;

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
use Symfony\Component\Yaml\Yaml;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;


class AcmeOutputListener implements BehatOutputListenerInterface {

    public function handleOutput(BehatOutputEvent $event)
    {
        //var_dump("Handle Output");
        //var_dump($event->getBuffer());
    }

}

class BehatEditorTest extends Base {


//    /**
//     * @test
//     */
//    public function listen_on_wrapper_non_sauce_prepare_event()
//    {
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../bin/';
//        $yaml = __DIR__ . '/../../../private/behat.yml';
//        $test = __DIR__ . '/../../../private/features/local.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Listeners
//
//        //This one gets Output while it is going line by line
//        $behat_wrapper->addOutputListener(new AcmeOutputListener());
//
//        //Add behat.command.prepare
//
//        $behat_wrapper->addPrepareListener(
//            new BehatPrepareListener(new BehatSetNewNameOnYaml())
//        );
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//    }
//
//    /**
//     * @expectedException \BehatWrapper\BehatException
//     *
//     * @test
//     */
//    public function thow_exception_on_test_fail()
//    {
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../bin/';
//        $yaml = __DIR__ . '/../../../private/behat.yml';
//        $test = __DIR__ . '/../../../private/features/fail_test.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//
//        $behat_wrapper->run($command);
//    }
//
//    /**
//     * @test
//     */
//    public function listen_on_wrapper_non_output_event()
//    {
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../bin/';
//        $yaml = __DIR__ . '/../../../private/behat.yml';
//        $test = __DIR__ . '/../../../private/features/local.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Listeners
//
//        //This one gets Output while it is going line by line
//        $behat_wrapper->addOutputListener(new AcmeOutputListener());
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//
//        //If the test fails this errors out!
//        // not output at this point
//
//        $output = $behat_wrapper->run($command);
//
//        $this->assertNotNull($output);
//    }
//
//    /**
//     * @test
//     */
//    public function listen_on_wrapper_phantom_prepare_event_set_tmp_folder()
//    {
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../bin/';
//        $yaml = __DIR__ . '/../../../private/behat.yml';
//        $test = __DIR__ . '/../../../private/features/local.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Listeners
//
//        //This one gets Output while it is going line by line
//        $behat_wrapper->addOutputListener(new AcmeOutputListener());
//
//        //Add behat.command.prepare
//        $setName = new BehatSetNewNameOnYaml();
//        $setName->setYmlName('PHP Unit Test');
//        $listener = new BehatPrepareListener($setName);
//
//        $behat_wrapper->addPrepareListener($listener);
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//
//        //@TODO how to much run the behat side of this for testing
//        $behat_wrapper->run($command);
//        $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
//    }

    /**
     * @test
     */
    public function listen_on_wrapper_phantom_prepare_event_set_name_now_in_yaml_file()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . '/../../../bin/';
        $yaml = __DIR__ . '/../../../private/behat.yml';
        $test = __DIR__ . '/../../../private/features/local.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners

        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new AcmeOutputListener());

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

//    /**
//     * @test
//    */
//    public function listen_on_wrapper_sauce_prepare_event_set_new_name()
//    {
//        $behat_wrapper = new BehatWrapper();
//        $bin = __DIR__ . '/../../../bin/';
//        $yaml = __DIR__ . '/../../../private/behat.yml';
//        $test = __DIR__ . '/../../../private/features/wikipedia.feature';
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Listeners
//
//        //This one gets Output while it is going line by line
//        $behat_wrapper->addOutputListener(new AcmeOutputListener());
//
//        $setName = new BehatSetNewNameOnYaml();
//        //$setName->setYmlName('PHP Unit Test');
//        $listener = new BehatPrepareListener($setName);
//
//        $behat_wrapper->addPrepareListener($listener);
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'saucelabs')
//            ->setTestPath($test);
//
//        //@TODO how to much run the behat side of this for testing
//        try {
//            $behat_wrapper->run($command);
//            $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
//        }
//        catch(\BehatWrapper\BehatException $e) {
//            //On a fail this is what kicks out
//            $this->assertContains('/tmp', $command->getOptions()['config']);
//        }
//    }

    //Test Get Sl Job
    //Test Get Output on Fail
    //Test Reporting API

} 