<?php

namespace BehatEditor\Tests\Sauce;

use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatEditor\SauceLabsSuccessListener;
use BehatEditor\SauceLabsErrorListener;
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
use BehatWrapper\Event\BehatSuccessEvent;
use BehatWrapper\Event\BehatSuccessListenerInterface;
use Symfony\Component\Filesystem\Filesystem;
use BehatEditor\BehatYmlParser;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use BehatEditor\BehatOutputListener;
use SauceLabs\Client;
use VCR\VCR;

use Dotenv;
\Dotenv::load(__DIR__.'/../../../../');


class BehatEditorSauceTest extends Base {

    const ROOT = '/../../../../';

    /**
    * The phantom test above will do this as well below just is so it can be seen in SL
    *
    *@test
    *
    */
    public function that_we_set_the_saucelabs_name_to_the_uuid()
    {
        //VCR::turnOn();
        //VCR::insertCassette('get_job_by_name');
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . self::ROOT . 'bin/';
        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
        $test = __DIR__ . self::ROOT . 'private/features/wikipedia.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners
        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        $setName = new BehatSetNewNameOnYaml();
        //$setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        $sauceClient = new \SauceLabs\Client();
        //@TODO how to much run the behat side of this for testing
        try {
            $behat_wrapper->run($command);
            $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
        }
        catch(\BehatWrapper\BehatException $e) {
            //On a fail this is what kicks out
            $this->assertContains('/tmp', $command->getOptions()['config']);
        }

        //Test is done so now look for it on sauce
        $username = $_ENV['USERNAME_KEY'];
        $sauceClient->authenticate($username, $_ENV['TOKEN_PASSWORD'], Client::AUTH_HTTP_PASSWORD);
        $field = 'name';
        $searchText = $setName->getUuid();
        $response = $sauceClient->api('jobs')->getJobsBy($username, $field, $searchText,
            $params = []);
        $this->assertNotEquals(0, count($response), sprintf("Failed count asserting job with name %s was found", $searchText));
        $jobID = $response[0]['id'];
        $jobInfo = $sauceClient->api('jobs')->getJob($username, $jobID);
        $this->assertEquals($searchText, $jobInfo['name'], sprintf("Failed finding job with name %s", $searchText));
        //VCR::eject();
        //VCR::turnOff();
    }

    /**
    * The phantom test above will do this as well below just is so it can be seen in SL
    *
    *@test
    *
    */
    public function that_we_set_the_saucelabs_passed_to_pass()
    {
        //VCR::turnOn();
        //VCR::insertCassette('get_job_by_name');
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . self::ROOT . 'bin/';
        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
        $test = __DIR__ . self::ROOT . 'private/features/wikipedia.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners
        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        $setName = new BehatSetNewNameOnYaml();
        //$setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        $sauceClient = new \SauceLabs\Client();
        //@TODO how to much run the behat side of this for testing
        try {
            $behat_wrapper->run($command);
            $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
            $status = 1; // Pass
        }
        catch(\BehatWrapper\BehatException $e) {
            //On a fail this is what kicks out
            $status = 0; // Fail
            $this->assertContains('/tmp', $command->getOptions()['config']);
        }

        //Test is done so now look for it on sauce
        $username = $_ENV['USERNAME_KEY'];
        $sauceClient->authenticate($username, $_ENV['TOKEN_PASSWORD'], Client::AUTH_HTTP_PASSWORD);

        //Get jobs matching this UUID and grab the latest one
        $field = 'name';
        $searchText = $setName->getUuid();
        $response = $sauceClient->api('jobs')->getJobsBy($username, $field, $searchText,
            $params = []);
        $jobID = $response[0]['id'];
        //Set the passed, some tags and a custom data
        $sauceClient->api('jobs')->updateJob($username, $jobID,
            [
                'passed' => $status,
                'tags'   => ['@tag1', '@tag2'],
                'custom-data' => [
                    'foo' => 'bar'
                ]
            ]
        );

        $jobInfo = $sauceClient->api('jobs')->getJob($username, $jobID);
        $this->assertEquals($status, $jobInfo['passed'], sprintf("Failed finding job with name %s", $searchText));

        //VCR::eject();
        //VCR::turnOff();
    }

    /**
    * The phantom test above will do this as well below just is so it can be seen in SL
    *
    *@test
    *
    */
    public function that_we_set_the_saucelabs_passed_set_to_pass_via_events()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . self::ROOT . 'bin/';
        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
        $test = __DIR__ . self::ROOT . 'private/features/wikipedia.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners
        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        $setName = new BehatSetNewNameOnYaml();
        //$setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $setSuccess = new SauceLabsSuccessListener();
        //@TODO get this from the behat_wrapper config like I do above
        $setSuccess->setUuid($setName->getUuid());
        $setSuccess->setTags(['@tag1', '@tag2']);
        $setSuccess->setCustomData(['foo' => 'bar']);

        $behat_wrapper->addSuccessListener($setSuccess);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        $sauceClient = new \SauceLabs\Client();
        //@TODO how to much run the behat side of this for testing
        try {
            $behat_wrapper->run($command);
            $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
            $status = 1; // Pass
        }
        catch(\BehatWrapper\BehatException $e) {
            //On a fail this is what kicks out
            $status = 0; // Fail
            $this->assertContains('/tmp', $command->getOptions()['config']);
        }

        //Test is done so now look for it on sauce
        $username = $_ENV['USERNAME_KEY'];
        $sauceClient->authenticate($username, $_ENV['TOKEN_PASSWORD'], Client::AUTH_HTTP_PASSWORD);

        //Get jobs matching this UUID and grab the latest one

        //This is now handled by the event
        $field = 'name';
        $searchText = $setName->getUuid();
        $response = $sauceClient->api('jobs')->getJobsBy($username, $field, $searchText,
            $params = []);
        $jobID = $response[0]['id'];
        //Make sure the event updated the status
        $jobInfo = $sauceClient->api('jobs')->getJob($username, $jobID);
        $this->assertEquals($status, $jobInfo['passed'], sprintf("Failed finding job with name %s", $searchText));

    }


    /**
     * The phantom test above will do this as well below just is so it can be seen in SL
     *
     *@test
     *
     */
    public function that_we_set_the_saucelabs_passed_set_to_fail_via_events()
    {
        $behat_wrapper = new BehatWrapper();
        $bin = __DIR__ . self::ROOT . 'bin/';
        $yaml = __DIR__ . self::ROOT . 'private/behat.yml';
        $test = __DIR__ . self::ROOT . 'private/features/wikipedia_fail.feature';

        $behat_wrapper->setBehatBinary($bin)->setTimeout(600);

        //Listeners
        //This one gets Output while it is going line by line
        $behat_wrapper->addOutputListener(new BehatOutputListener());

        $setName = new BehatSetNewNameOnYaml();
        //$setName->setYmlName('PHP Unit Test');
        $listener = new BehatPrepareListener($setName);

        $behat_wrapper->addPrepareListener($listener);

        $setSuccess = new SauceLabsSuccessListener();
        //@TODO get this from the behat_wrapper config like I do above
        $setSuccess->setUuid($setName->getUuid());
        $setSuccess->setTags(['@tag1', '@tag2']);
        $setSuccess->setCustomData(['foo' => 'bar']);

        $behat_wrapper->addSuccessListener($setSuccess);


        $setError = new SauceLabsErrorListener();
        //@TODO get this from the behat_wrapper config like I do above
        $setError->setUuid($setName->getUuid());
        $setError->setTags(['@tag1', '@tag2']);
        $setError->setCustomData(['foo' => 'bar']);

        $behat_wrapper->addErrorListener($setError);

        $command = BehatCommand::getInstance()
            ->setOption('config', $yaml)
            ->setOption('profile', 'saucelabs')
            ->setTestPath($test);

        $sauceClient = new \SauceLabs\Client();
        //@TODO how to much run the behat side of this for testing
        try {
            $behat_wrapper->run($command);
            $this->assertContains($setName->getNewName(), $command->getOptions()['config']);
            $status = 1; // Pass
        }
        catch(\BehatWrapper\BehatException $e) {
            //On a fail this is what kicks out
            $status = 0; // Fail
            $this->assertContains('/tmp', $command->getOptions()['config']);
        }

        //Test is done so now look for it on sauce
        $username = $_ENV['USERNAME_KEY'];
        $sauceClient->authenticate($username, $_ENV['TOKEN_PASSWORD'], Client::AUTH_HTTP_PASSWORD);

        //Get jobs matching this UUID and grab the latest one

        //This is now handled by the event
        $field = 'name';
        $searchText = $setName->getUuid();
        $response = $sauceClient->api('jobs')->getJobsBy($username, $field, $searchText,
            $params = []);
        $jobID = $response[0]['id'];
        //Make sure the event updated the status
        $jobInfo = $sauceClient->api('jobs')->getJob($username, $jobID);
        $this->assertEquals($status, $jobInfo['passed'], sprintf("Failed finding job with name %s", $searchText));

    }
    #assets


} 
