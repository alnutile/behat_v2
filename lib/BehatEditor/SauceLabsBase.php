<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 1:21 PM
 */

namespace BehatEditor;

use BehatEditor\Tests\BaseTest;
use SauceLabs\Client;

use Dotenv;
\Dotenv::load(__DIR__.'/../../');

abstract class SauceLabsBase {
    use BehatEditorTraits;

    protected $saucelabsClient;
    protected $activeId;
    protected $event_object;
    protected $jobsFound;

    public function __construct(\SauceLabs\Client $sauceLabsClient = null)
    {
        $this->saucelabsClient = $sauceLabsClient;
    }

    protected function updateCustomData()
    {
        $custom_data = $this->event_object->getWrapper()->getCustomData();
        if(count($custom_data) > 0) {
            $this->auth();
            $this->getSaucelabsClient()->api('jobs')->updateJob(
                $this->getUserName(),
                $this->getJobId(),
                $custom_data);
        }
        return $this;
    }

    protected function updateStatus()
    {
        $this->auth();
        $this->getSaucelabsClient()->api('jobs')->updateJob(
            $this->getUserName(),
            $this->getJobId(),
            ['passed' => $this->getStatus()]);
        return $this;
    }


    protected function updateTags()
    {
        $tags = $this->event_object->getWrapper()->getTags();
        if(count($tags) > 0) {
            $this->auth();
            $this->getSaucelabsClient()->api('jobs')->updateJob(
                $this->getUserName(),
                $this->getJobId(),
                ['tags' => $tags]);
        }
        return $this;
    }

    public function getJobId()
    {
        if(!$this->activeId) {
            $this->setJobId();
        }

        return $this->activeId;
    }

    public function setJobId($id = null)
    {
        $this->auth();

        $getJobsWithName = $this->getSaucelabsClient()->api('jobs')->getJobsBy(
        $this->getUserName(), 'name', $this->event_object->getWrapper()->getUuid());

        if(empty($getJobsWithName)) {
            throw new \Exception("Failed finding job with the name @ " . $this->event_object->getWrapper()->getUuid());
        }
        $this->jobsFound = $getJobsWithName;
        $this->activeId = $getJobsWithName[0]['id'];
        //Setting on Event as well
        $this->event_object->getWrapper()->setRemoteTestingServiceId($this->activeId);

        return $this;
    }

    public function auth()
    {
        $this->getSaucelabsClient()->authenticate(
            $this->getUserName(), $this->getToken(), $this->getAuth()
        );
        return $this;
    }

    public function getAuth()
    {
        return Client::AUTH_HTTP_PASSWORD;
    }

    public function getUserName()
    {
        if(!$_ENV['USERNAME_KEY']) {
            throw new \Exception("Saucelabs username not set");
        }
        return $_ENV['USERNAME_KEY'];
    }

    public function getToken()
    {
        if(!$_ENV['TOKEN_PASSWORD']) {
            throw new \Exception("Saucelabs token/password not set");
        }
        return $_ENV['TOKEN_PASSWORD'];
    }

    /**
     * @return \SauceLabs\Client
     */
    public function getSaucelabsClient()
    {
        if ($this->saucelabsClient === null)
        {
            $this->setSaucelabsClient();
        }
        return $this->saucelabsClient;
    }

    /**
     * @param \SauceLabs\Client $saucelabsClient
     * @return $this
     */
    public function setSaucelabsClient($saucelabsClient = null)
    {
        if($saucelabsClient === null) {
            $this->saucelabsClient = new \SauceLabs\Client();
        } else {
            $this->saucelabsClient = $saucelabsClient;
        }
        return $this;
    }

} 