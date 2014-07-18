<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/11/14
 * Time: 11:32 AM
 */

namespace BehatEditor;

use BehatEditor\Controllers\BehatRunController;
use BehatEditor\Reporting\BehatReportingErrorListener;
use BehatEditor\Reporting\BehatReportingListener;
use BehatEditor\SauceLabs\SauceLabsErrorListener;
use BehatEditor\SauceLabs\SauceLabsSuccessListener;
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
        $this->setProfile('saucelabs');
        $this->getBehatWrapper()
            ->setUuid()
            ->setTimeout(600)
            ->setBehatBinary($this->getBin());
        $this->setPrepareListeners();
        $this->setOutputListeners();
        $this->setErrorListeners();
        $this->setSuccessListeners();
    }

    public function setPrepareListeners()
    {
        $setName = new BehatSetNewNameOnYaml();
        $listener = new BehatPrepareListener($setName);
        $this->getBehatWrapper()->addPrepareListener($listener);
        return $this;
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

    private function setOutputListeners()
    {
        $this->getBehatWrapper()->addOutputListener(new BehatOutputListener());
    }

    private function setErrorListeners()
    {
        $setError = new SauceLabsErrorListener();
        $this->getBehatWrapper()->addErrorListener($setError);
        $reportError = new BehatReportingErrorListener();
        $this->getBehatWrapper()->addErrorListener($reportError);
    }

    private function setSuccessListeners()
    {
        $setSuccess = new SauceLabsSuccessListener();
        $this->getBehatWrapper()->addSuccessListener($setSuccess);
        $reportListener = new BehatReportingListener();
        $this->getBehatWrapper()->addSuccessListener($reportListener);
    }


} 