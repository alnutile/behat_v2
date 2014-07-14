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

/**
 * Setup the new name on the yaml file
 * so that the tests are easier to track
 *
 * Class BehatSetNewNameOnYaml
 * @package BehatEditor
 */
class BehatSetNewNameOnYaml {

    const TEMP = '/tmp';

    protected $event;
    protected $profile_key;
    protected $updated_yaml;
    protected $ymlArray;
    protected $new_name;
    protected $destination;
    /**
     * @var BehatYmlParser
     */
    private $behatYmlParser;

    public function __construct(BehatYmlParser $behatYmlParser = null)
    {
        $this->behatYmlParser = $behatYmlParser;
    }

    public function setBehatYmlParser(BehatYmlParser $behatYmlParser)
    {
        $this->behatYmlParser = $behatYmlParser;
        return $this;
    }
    
    public function getBehatYmlParser()
    {
        if(null === $this->behatYmlParser) {
            $this->behatYmlParser = new BehatYmlParser(new Filesystem(), new Yaml());
        }
        return $this->behatYmlParser;
    }

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
        //set the options to the class
        //update the yml file so that the name is what this class wants it
        //to be

        $this->getBehatYmlParser()->setOptions($this->event->getOptions());


        //If we are dealing with Saucelabs then set the name
        if($this->checkIfNameAvailable()) {
            //There is a name so it is time to update the name and the yaml path
            $this->updated_yaml = $this->getYmlArray();
            $this->updated_yaml[$this->profile_key]['extensions']['Behat\MinkExtension\Extension']['selenium2']['capabilities']['name'] = $this->getName();
            //@TODO might be best to move this to it's own step
            $this->saveToTmp();
            $this->event->getCommand()->setOption('config', $this->getDestination());
        }
    }

    public function saveToTmp()
    {
        $output = $this->behatYmlParser->getYaml()->dump($this->updated_yaml);
        $this->behatYmlParser->getFilesystem()->dumpFile($this->getDestination(), $output);
    }

    public function getDestination()
    {
        if(null === $this->destination) {
            $this->setDestination();
        }
        return $this->destination;
    }

    public function setDestination($destination = null)
    {
        if($destination === null) {
            $this->destination = self::TEMP . '/' . $this->getNewName();
        } else {
            $this->destination = $destination;
        }
        return $this;
    }

    public function getNewName()
    {
        if(null === $this->new_name) {
            $this->setNewName();
        }
        return $this->new_name;
    }

    public function setNewName($name = null)
    {
       if(null === $name) { $this->new_name = date('U') . '_behat.yml'; };
       return $this;
    }

    public function getName()
    {
        return "New Name Here";
    }

    public function checkIfNameAvailable()
    {
        //1. if no profile set then assume default else use the key
        //2. using the profile name get the settings
        //3. look for extensions.Behat\Mink.selenium2.capabilities.name
        //4. if it is there
        //   a. copy to tmp
        //   b. reset that to something that is stored for later
        //   c. reset the config path to this new path
        //5. if it is NOT there
        //   a. set a default name for the report
        $this->getProfileKey();

        if(isset($this->getYmlArray()[$this->profile_key]["extensions"])) {
            if(isset($this->getYmlArray()[$this->profile_key]["extensions"]['Behat\MinkExtension\Extension']['selenium2']['capabilities']['name'])) {
                //step 4 copy to tmp and overwrite
                return true;
            }
        }
        return false;

        //return $this->getBehatYmlParser()->getYamlOption('name');
    }

    public function getYmlArray() {
        if(null === $this->ymlArray) {
            $this->setYmlArray();
        }
        return $this->ymlArray;
    }

    public function setYmlArray()
    {
        $this->ymlArray = $this->getBehatYmlParser()->getYamlToArray();
        return $this;
    }

    public function getProfileKey()
    {
        if(null === $this->profile_key)
        {
            $this->setProfileKey();
        }
        return $this->profile_key;
    }

    public function setProfileKey()
    {
        if(!$this->getBehatYmlParser()->getOption('profile')) {
            //Assume default
            $this->profile_key = 'default';
        } else {
            $this->profile_key = $this->getBehatYmlParser()->getOption('profile');
        }
        return $this;
    }
} 