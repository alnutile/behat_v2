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
    protected $tags = [];
    protected $custom_data = [];

    public function __construct(\SauceLabs\Client $sauceLabsClient = null)
    {
        $this->saucelabsClient = $sauceLabsClient;
    }

    protected function updateCustomData($event)
    {
        $this->auth();
        $this->getSaucelabsClient()->api('jobs')->updateJob(
            $this->getUserName(),
            $this->getJobId(),
            $this->getCustomData());
        return $this;
    }

    public function getCustomData()
    {
        return $this->custom_data;
    }

    public function setCustomData(array $custom_data)
    {
        $this->custom_data = array_merge($this->custom_data, $custom_data);
        return $this;
    }

    protected function updateStatus($event)
    {
        $this->auth();
        $this->getSaucelabsClient()->api('jobs')->updateJob(
            $this->getUserName(),
            $this->getJobId(),
            ['passed' => $this->getStatus()]);
        return $this;
    }


    protected function updateTags($event)
    {
        $this->auth();
        $this->getSaucelabsClient()->api('jobs')->updateJob(
            $this->getUserName(),
            $this->getJobId(),
            ['tags' => $this->getTags()]);
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = array_merge($this->tags, $tags);
        return $this;
    }

    public function getJobId()
    {
        if($this->activeId) {
            return $this->activeId;
        }

        $this->auth();

        try {
            $getJobsWithName = $this->getSaucelabsClient()->api('jobs')->getJobsBy(
                $this->getUserName(), 'name', $this->getUuid()
            );
            $this->activeId = $getJobsWithName[0]['id'];
            return $this->activeId;

        } catch(\Exception $e) {
            throw new \Exception("Failed finding job with the name " . $this->getUuid());
        }
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