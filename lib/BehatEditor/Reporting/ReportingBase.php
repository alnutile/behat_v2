<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/18/14
 * Time: 5:58 AM
 */

namespace BehatEditor\Reporting;


abstract class ReportingBase {

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
    protected $event;
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


    public function optionsFromYaml()
    {
        if(!$this->event->getCommand()->getOptions()['config'])
        {
            return $this->options = [];
        }
        $path                   = $this->event->getCommand()->getOptions()['config'];
        $this->active_profile   = $this->event->getCommand()->getOptions()['profile'];

        $this->getBehatYmlParser()->setYamlToArray($path);
        $this->pullOutAllOptionsFromActiveProfile();
    }

    protected function pullOutAllOptionsFromActiveProfile()
    {
        $this->default_profile_options  = $this->getBehatYmlParser()->pluckFromYmlArray('default');
        $this->setAllDataValues($this->default_profile_options);

        $this->active_profile_options   = $this->getBehatYmlParser()->pluckFromYmlArray($this->active_profile);
        $this->setAllDataValues($this->active_profile_options);
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
        $this->data_values['job_uuid'] = $this->event->getWrapper()->getUuid();
    }

    public function setOutput()
    {
        $this->data_values['output'] = $this->event->getProcess()->getOutput();
    }

    public function setBranch()
    {
        $this->data_values['branch'] = $this->event->getWrapper()->getBranch();
    }

    public function setRepoName()
    {
        $this->data_values['repo_name'] = $this->event->getWrapper()->getRepoName();
    }

    public function setFileName()
    {
        $this->data_values['file_name'] = $this->event->getWrapper()->getFileName();
    }


    public function setRemoteJobId()
    {
        $this->data_values['remote_job_id'] = $this->event->getWrapper()->getRemoteTestingServiceId();
    }

    public function setTagsValue()
    {
        $this->data_values['tags'] = $this->event->getWrapper()->getTags();
    }

    public function setCustomDataValue()
    {
        $this->data_values['custom_data'] = $this->event->getWrapper()->getCustomData();
    }


} 