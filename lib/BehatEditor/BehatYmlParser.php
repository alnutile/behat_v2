<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/14/14
 * Time: 7:54 AM
 */

namespace BehatEditor;

use Behat\Mink\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class BehatYmlParser {

    /**
     * Ideally this would be the
     * system temp
     */
    const TEMP = '/tmp';

    protected $options;
    protected $yml_original_path;
    protected $yml_original;
    protected $yml_updated;
    protected $storageFolder;
    protected $yamlToArray;
    /**
     * @var
     */
    protected $filesystem;
    /**
     * @var \Symfony\Component\Yaml\Yaml
     */
    protected $yaml;

    public function getFilesystem()
    {
        return (null === $this->filesystem) ? $this->setFilesystem()->filesystem : $this->filesystem;
    }

    public function setFilesystem($filesystem = null)
    {
        if(null === $filesystem) { $this->filesystem = new Filesystem(); } else { $this->filesystem = $filesystem; }
        return $this;
    }

    public function getYaml()
    {
        return (null === $this->yaml) ? $this->setYaml()->yaml : $this->yaml;
    }

    public function setYaml($yaml = null)
    {
        if(null === $yaml) { $this->yaml = new Yaml(); } else { $this->yaml = $yaml; }
        return $this;
    }

    public function __construct(Filesystem $filesystem, Yaml $yaml)
    {
        $this->filesystem = $filesystem;
        $this->yaml = $yaml;
    }

    public function modifyBehatYml()
    {
        //see if there is a yml file
        // if so get its info
        // replace the info with the
        // name of the test so we can track it later
        // since I can not write to this path I will
        // set a new tmp area
        if(isset($this->options['config'])) {
            $this->setOriginalPath($this->options['config']);
        }
    }



    public function setOriginalPath($path)
    {
        $this->yml_original_path = $path;
        return $this;
    }

    public function getOriginalPath()
    {
        return $this->yml_original_path;
    }

    public function setStorageFolder($folder)
    {
        $this->storageFolder = $folder;
        return $this;
    }

    public function getStorageFolder()
    {
        if(!$this->storageFolder) {
            return self::TEMP;
        }
        return $this->storageFolder;
    }

    public function getOption($key)
    {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }
        return false;
    }

    public function getYamlOption($option)
    {
        $yaml = $this->getYamlToArray();
        return $yaml;
    }

    public function getYamlToArray()
    {
        if($this->yamlToArray) {
            return $this->yamlToArray;
        }
        $this->setYamlToArray();
        return $this->yamlToArray;
    }

    public function setYamlToArray()
    {
        if($this->getOption('config'))
        {
            //set the array
            //read it and set it
            $this->yamlToArray = $this->yaml->parse($this->getOption('config'));
            return $this;
        }
        $this->yamlToArray = [];
        return $this;
    }

    public function setOption($key, $value)
    {
        if(isset($this->yamlToArray[$key])) {
            $this->yamlToArray[$key] = $value;
        } else {
            throw new \Exception("Key $key did not exist");
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

} 