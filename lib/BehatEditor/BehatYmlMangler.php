<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/14/14
 * Time: 12:28 PM
 */

namespace BehatEditor;

use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * This trait relies on BehatEditorTraits
 *
 * Class BehatYmlMangler
 * @package BehatEditor
 */
trait BehatYmlMangler {

    protected $profile_key;
    protected $updated_yaml;
    protected $ymlPath;
    protected $ymlArray;

    protected $destination;

    protected $temp = '/tmp';


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

    public function saveToTmp()
    {
        $output = $this->behatYmlParser->getYaml()->dump($this->updated_yaml);
        $this->behatYmlParser->getFilesystem()->dumpFile($this->getDestination(), $output);
    }

    public function getTemp()
    {
        return $this->temp;
    }

    public function setTemp($temp)
    {
        $this->temp = $temp;
        return $this;
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
            $this->destination = $this->getTemp() . '/' . $this->getNewName();
        } else {
            $this->destination = $destination;
        }
        return $this;
    }

    public function setYmlPath($path)
    {
        $this->ymlPath = $path;
        return $this;
    }

    public function getYmlPath()
    {
        return $this->ymlPath;
    }

    public function getNewName()
    {
        return $this->new_name;
    }

    public function setNewName($name)
    {
        $this->new_name = str_replace(' ', '_', $name) . '_behat.yml';
        return $this;
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