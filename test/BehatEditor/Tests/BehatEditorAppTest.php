<?php

namespace BehatEditor\Tests;

use BehatWrapper\BehatCommand;
use BehatEditor\Tests\BaseTest;
use BehatWrapper\BehatWrapper;
use BehatWrapper\Event\BehatEvent;
use BehatWrapper\Event\BehatEvents;
use BehatWrapper\Event\BehatPrepareEvent;
use BehatWrapper\Event\BehatOutputEvent;
use BehatWrapper\Event\BehatOutputListenerInterface;
use BehatWrapper\Event\BehatPrepareListenerInterface;

class AcmePrepareListener implements BehatPrepareListenerInterface {


    public function handlePrepare(BehatEvent $event)
    {
        var_dump("HandlePrepare");
        $options = $event->getOptions();
        if(!empty($options)) {
            var_dump($options);
        }
        //Get config key
        // update ad needed
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


    /**
     * @test
     */
    public function listen_on_wrapper()
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
        $behat_wrapper->addPrepareListener(new AcmePrepareListener());


        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
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