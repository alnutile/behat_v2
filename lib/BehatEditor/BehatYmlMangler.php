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

abstract class BehatYmlMangler {

    const TEMP = '/tmp';

    protected $event;
    protected $profile_key;
    protected $updated_yaml;
    protected $ymlArray;

    protected $destination;
    protected $uuid;
    protected $new_name;

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
        if(null === $name) {
            $this->new_name = $this->getUuid() . '_behat.yml';
        } else {
            $this->new_name = str_replace(' ', '_', $name) . '_behat.yml';
        }
        return $this;
    }

    public function getUuid()
    {
        if (null === $this->uuid)
        {
           $this->setUuid();
        }
        return $this->uuid;
    }

    public function setUuid($uuid = null)
    {
        if($uuid) {
            $this->uuid = $uuid;
            return $this;
        }
        $this->uuid = Uuid::uuid4()->toString();
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