<?php

namespace BehatEditor\Tests\Reporting;

use BehatEditor\BehatEditorBehatWrapper;
use BehatEditor\BehatEditorTraits;
use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatEditor\BehatReportingListener;
use BehatEditor\SauceLabsSuccessListener;
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
use BehatWrapper\Event\BehatSuccessListenerInterface;
use Symfony\Component\Filesystem\Filesystem;
use BehatEditor\BehatYmlParser;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use BehatEditor\BehatOutputListener;

class ReportingTest extends Base {
    const ROOT = '/../../../../';
//    /**
//     * @test
//     */
//    public function add_reporting_event_check_the_data_values_being_reported()
//    {
//        $behat_wrapper = new BehatEditorBehatWrapper();
//        $bin = __DIR__ . self::ROOT . 'bin/';
//        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
//        $test = __DIR__ . self::ROOT . 'private/features/local.feature';
//
//        //Typically a test comes in via url or command line with three args to start
//        // 1. repo_name
//        // 2. branch
//        // 3. filename
//
//        //The rest is sent in via params via POST or PUT unless I stick with GET not sure
//
//        $behat_wrapper->setBranch('master');
//        $behat_wrapper->setRepoName('bbbbb_test');
//        $behat_wrapper->setFilename('local.feature');
//
//        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);
//
//        //Listeners
//
//        //This one gets Output while it is going line by line
//        $behat_wrapper->addOutputListener(new BehatOutputListener());
//
//
//        //Add behat.command.prepare
//        $setName = new BehatSetNewNameOnYaml();
//        $listener = new BehatPrepareListener($setName);
//        $behat_wrapper->addPrepareListener($listener);
//
//        $behat_wrapper->setUuid($setName->getUuid());
//
//        //Add a report listener
//        $reportListener = new BehatReportingListener();
//
//        $behat_wrapper->addSuccessListener($reportListener);
//
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setOption('profile', 'phantom')
//            ->setTestPath($test);
//
//        //@TODO how to much run the behat side of this for testing
//        $behat_wrapper->run($command);
//        $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
//        $this->assertEquals('local.feature', $reportListener->getDataValues()['file_name']);
//        $this->assertEquals('1', $reportListener->getDataValues()['status']);
//        var_dump("Now from the test file");
//        var_dump($reportListener->getDataValues());
//    }

    /**
     * This one is slow cause it uses saucelabs so
     * will not run all the time
     *
     * @test
     */
    public function add_reporting_event_check_that_saucelabs_job_id_is_added()
    {
        $behat_wrapper = new BehatEditorBehatWrapper();
        $bin = __DIR__ . self::ROOT . 'bin/';
        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
        $test = __DIR__ . self::ROOT . 'private/features/wikipedia.feature';

        //Typically a test comes in via url or command line with three args to start
        // 1. repo_name
        // 2. branch
        // 3. filename

        //The rest is sent in via params via POST or PUT unless I stick with GET not sure

        $behat_wrapper->setBranch('master');
        $behat_wrapper->setRepoName('bbbbb_test');
        $behat_wrapper->setFilename('local.feature');

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners
        $setSuccess = new SauceLabsSuccessListener();
        $behat_wrapper->addSuccessListener($setSuccess);

        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        //Add behat.command.prepare
        $setName = new BehatSetNewNameOnYaml();
        $listener = new BehatPrepareListener($setName);
        $behat_wrapper->addPrepareListener($listener);

        $behat_wrapper->setUuid($setName->getUuid());

        //Add a report listener
        $reportListener = new BehatReportingListener();
        $behat_wrapper->addSuccessListener($reportListener);


        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        //@TODO how to much run the behat side of this for testing
        $behat_wrapper->run($command);
        $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
        $this->assertEquals('local.feature', $reportListener->getDataValues()['file_name']);
        $this->assertEquals('1', $reportListener->getDataValues()['status']);
        var_dump("Now from the test file");
        var_dump($reportListener->getDataValues());
    }

} 