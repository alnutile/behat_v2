<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 3:09 PM
 */

namespace BehatEditor;


use BehatWrapper\BehatWrapper;

class BehatEditorBehatWrapper extends BehatWrapper {

    protected $uuid;
    protected $behat_yml_array = [];

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function setBehatYmlArray(array $yaml)
    {
        $this->behat_yml_array = $yaml;
        return $this;
    }

    public function getBehatYmlArray()
    {
        return $this->behat_yml_array;
    }


} 