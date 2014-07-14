<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/14/14
 * Time: 8:36 AM
 */

namespace BehatEditor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use BehatEditor\BehatYmlParser;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * Setup the new name on the yaml file
 * so that the tests are easier to track
 *
 * Class BehatSetNewNameOnYaml
 * @package BehatEditor
 */
class BehatSetNewNameOnYaml extends BehatYmlMangler {

    protected $yml_name;
    protected $new_name;

    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    public function getEvent()
    {
        return $this->event;
    }


    public function setName()
    {
        $this->getBehatYmlParser()->setOptions($this->event->getOptions());

        if($this->checkIfNameAvailable()) {
            $this->updated_yaml = $this->getYmlArray();
            $this->updated_yaml[$this->profile_key]['extensions']['Behat\MinkExtension\Extension']['selenium2']['capabilities']['name'] = $this->getYmlName();

            //@TODO might be best to move this to it's own step
            $this->saveToTmp();
            $this->event->getCommand()->setOption('config', $this->getDestination());
        }
    }

    public function getYmlName()
    {
        if (null === $this->yml_name)
        {
            $this->setYmlName();
        }
        return $this->yml_name;
    }

    public function setYmlName($yml_name = null)
    {
        $this->yml_name = ($yml_name === null) ? $this->getUuid() : $yml_name;
        return $this;
    }

    public function checkIfNameAvailable()
    {
        $this->getProfileKey();

        if(isset($this->getYmlArray()[$this->profile_key]["extensions"])) {
            if(isset($this->getYmlArray()[$this->profile_key]["extensions"]['Behat\MinkExtension\Extension']['selenium2']['capabilities']['name'])) {
                //step 4 copy to tmp and overwrite
                return true;
            }
        }
        return false;
    }




} 