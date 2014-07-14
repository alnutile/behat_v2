<?php

namespace BehatEditor\Tests;

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

/**
 * Get the options before they are run and modify as needed.
 *
 *
 * Class AcmePrepareListener
 * @package BehatEditor\Tests
 */
class AcmePrepareListener implements BehatPrepareListenerInterface {

    /**
     * @var
     */
    private $event;
    /**
     * @var \BehatEditor\BehatSetNewNameOnYaml
     */
    private $behatSetNewNameOnYaml;

    public function __construct(BehatSetNewNameOnYaml $behatSetNewNameOnYaml)
    {

        $this->behatSetNewNameOnYaml = $behatSetNewNameOnYaml;
    }

    public function handlePrepare(BehatPrepareEvent $event)
    {
        var_dump("HandlePrepare");
        $this->event = $event;
        $this->behatSetNewNameOnYaml->setEvent($event)->setName();
    }



}

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
//    public function listen_on_wrapper_non_sauce()
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
//            new AcmePrepareListener(new BehatSetNewNameOnYaml())
//        );
//
//
//        $command = BehatCommand::getInstance()
//            ->setOption('config', $yaml)
//            ->setTestPath($test);
//
//        //If the test fails this errors out!
//        // not output at this point
//
//        $output = $behat_wrapper->run($command);
//
//        //How to set the behat.yml name of a test
//        //otherwise finding it in sl is hard
//
//        //How to get the output on the fly
//
//        //How to get the report after the test is done via events
//
//        //How to update SL after the test is done via it's api
//
//        //var_dump($output);
//    }

    /**
     * @test
     */
    public function listen_on_wrapper_sauce()
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

        $behat_wrapper->addPrepareListener(
            new AcmePrepareListener(new BehatSetNewNameOnYaml()));


        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        //If the test fails this errors out!
        // not output at this point

        $output = $behat_wrapper->run($command);

        //How to set the behat.yml name of a test
        //otherwise finding it in sl is hard

        //How to get the output on the fly

        //How to get the report after the test is done via events

        //How to update SL after the test is done via it's api

        //var_dump($output);
    }

} 