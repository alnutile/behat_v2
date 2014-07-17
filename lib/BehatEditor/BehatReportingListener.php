<?php

namespace BehatEditor;

use BehatEditor\BehatEditorBehatWrapper;
use BehatEditor\BehatEditorTraits;
use BehatEditor\BehatPrepareListener;
use BehatEditor\BehatSetNewNameOnYaml;
use BehatEditor\BehatYmlMangler;
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

class BehatReportingListener implements BehatSuccessListenerInterface {
    use BehatEditorTraits, BehatYmlMangler;

    protected $options;
    protected $browser_version;
    protected $browser;
    protected $operating_system;
    protected $repo;
    protected $branch;
    protected $filename;
    protected $user_uuid;
    protected $remote_job_id;
    protected $results_output;
    protected $created_at;
    protected $original_yml_file;
    protected $base_url;
    protected $active_profile;
    protected $active_profile_options;
    protected $default_profile_options;
    protected $success_event;
    protected $merged_profile_options = [];
    protected $data_values = [
        'browser' => null,
        'name' => null,
        'version' => null,
        'platform' => null,
        'base_url' => null,
        'repo_name' => null,
        'file_name' => null,
        'branch' => null,
        'status' => null,
        'user_uuid' => null,
        'remote_job_id' => null,
        'job_uuid'  => null,
        'output'  => null,
        'tags' => []
    ];


    /**
     * @param $event BehatEvent
     */
    public function handleSuccess($event) {
        $this->success_event = $event;
        var_dump("Time to report");
        //Get UUID
        //var_dump($event->getWrapper()->getUuid());

        //Get the path to the yml file or yaml array
        // so we can grab info
        $this->optionsFromYaml();
        $this->setOutput();
        $this->setJobUuid();
        $this->setBranch();
        $this->setFileName();
        $this->setRepoName();
        $this->reportDataValueStatus();
    }

    public function optionsFromYaml()
    {
        if(!$this->success_event->getCommand()->getOptions()['config'])
        {
            return $this->options = [];
        }
        $path                   = $this->success_event->getCommand()->getOptions()['config'];
        $this->active_profile   = $this->success_event->getCommand()->getOptions()['profile'];

        $this->getBehatYmlParser()->setYamlToArray($path);
        $this->pullOutAllOptionsFromActiveProfile();
    }

    protected function pullOutAllOptionsFromActiveProfile()
    {
        $this->default_profile_options  = $this->getBehatYmlParser()->pluckFromYmlArray('default');
        $this->setAllDataValues($this->default_profile_options);

        $this->active_profile_options   = $this->getBehatYmlParser()->pluckFromYmlArray($this->active_profile);
        $this->setAllDataValues($this->active_profile_options);
        //$this->merged_profile_options   = array_merge($this->default_profile_options, $this->active_profile_options);
    }

    protected function setAllDataValues($array) {
        $traverse = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($array));
        foreach($traverse as $key=>$value)
        {
            if(array_key_exists($key, $this->data_values))
            {
                $this->setDataValues($key, $value);
            }
        }
    }

    public function getDataValues()
    {
        return $this->data_values;
    }

    public function setDataValues($key, $value)
    {
        $this->data_values[$key] = $value;
    }

    public function setJobUuid()
    {
        $this->data_values['job_uuid'] = $this->success_event->getWrapper()->getUuid();
    }

    public function setOutput()
    {
        $this->data_values['output'] = $this->success_event->getProcess()->getOutput();
    }

    public function setBranch()
    {
        $this->data_values['branch'] = $this->success_event->getWrapper()->getBranch();
    }

    public function setRepoName()
    {
        $this->data_values['repo_name'] = $this->success_event->getWrapper()->getRepoName();
    }

    public function setFileName()
    {
        $this->data_values['file_name'] = $this->success_event->getWrapper()->getFileName();
    }

    public function reportDataValueStatus()
    {
        $this->data_values['status'] = 1;
    }

    public function setRemoteJobId()
    {
        $this->data_values['remote_job_id'] = $this->success_event->getWrapper()->setRemoteTestingServiceId();
    }
}
