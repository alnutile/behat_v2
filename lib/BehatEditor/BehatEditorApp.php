<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/11/14
 * Time: 11:32 AM
 */

namespace BehatEditor;

use BehatEditor\Controllers\BehatRunController;
use BehatWrapper\BehatCommand;
use BehatWrapper\BehatException;
use BehatWrapper\BehatWrapper;
use Dotenv;
\Dotenv::load(__DIR__.'/../../');

class BehatEditorApp {
    protected $runController;
    protected $base = "/../../";
    protected $bin;
    protected $yml_path;
    protected $profile = 'phantom';

    /**
     * @var \BehatWrapper\BehatWrapper
     */
    private $behatWrapper;

    public function __construct(BehatEditorBehatWrapper $behatWrapper = null, BehatCommand $behatCommand = null)
    {

        $this->behatWrapper = ($behatWrapper == null) ? new BehatEditorBehatWrapper() : $behatWrapper;
        $this->behatCommand = $behatCommand;
        $this->getBehatWrapper()
            ->setBehatBinary($this->getBin());
    }

    public function getBehatCommand()
    {
        if(!$this->behatCommand)
        {
            $this->setBehatCommand();
        }
        return $this->behatCommand;
    }

    public function setBehatCommand($args = array())
    {
        $this->behatCommand = BehatCommand::getInstance($args)
            ->setOption('config', $this->getYmlPath())
            ->setOption('profile', $this->getProfile());
        return $this;
    }

    public function getBehatWrapper()
    {
        return $this->behatWrapper;
    }

    /**
     * Run a test
     *
     */
    public function run($repo_setting_id, $filename)
    {
        return $this->getRunController()->run($repo_setting_id, $filename);
    }

    public function getRunController()
    {
        if(!$this->runController)
        {
            $this->setRunController();
        }
        return $this->runController;
    }

    public function setRunController()
    {
        $this->runController = new BehatRunController(null, $this);
        return $this;
    }

    public function setBin($bin = null)
    {
        if($bin == null) {
            $this->bin = __DIR__ . $this->base . '/bin/';
        }
    }

    public function getBin()
    {
        if(!$this->bin) {
            $this->setBin();
        }
        return $this->bin;
    }

    /**
     * @return mixed
     */
    public function getYmlPath()
    {
        if(!$this->yml_path)
        {
            $this->setYmlPath();
        }
        return $this->yml_path;
    }

    /**
     * @param mixed $yml_path
     * @return $this
     */
    public function setYmlPath($yml_path = null)
    {
        if($yml_path == null) {
            $yml_path = __DIR__ . $this->base . 'private/behat.yml';
        }
        $this->yml_path = $yml_path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $profile;
    }


} 