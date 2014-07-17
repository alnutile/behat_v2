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
        'repo' => null,
        'filename' => null,
        'branch' => null,
        'status' => null,
        'user_uuid' => null,
        'remote_job_id' => null,
        'job_id'  => null,
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
        $this->optionsFromYaml($event);

        //Finally get the output
        //var_dump($event->getProcess()->getOutput());
        var_dump($this->data_values);
    }

    public function optionsFromYaml($event)
    {
        if(!$event->getCommand()->getOptions()['config'])
        {
            return $this->options = [];
        }
        $path                   = $event->getCommand()->getOptions()['config'];
        $this->active_profile   = $event->getCommand()->getOptions()['profile'];

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

    public function setDataValues($key, $value)
    {
        $this->data_values[$key] = $value;
    }

}
