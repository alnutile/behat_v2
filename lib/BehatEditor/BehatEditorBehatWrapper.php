<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 3:09 PM
 */

namespace BehatEditor;


use BehatWrapper\BehatWrapper;
use Rhumsaa\Uuid\Uuid;

class BehatEditorBehatWrapper extends BehatWrapper {

    protected $uuid;
    protected $behat_yml_array = [];
    protected $repo_name;
    protected $branch;
    protected $filename;
    protected $tags = [];
    protected $custom_data = [];
    protected $remoteTestingServiceId;

    /**
     * @param mixed $branch
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $repo_name
     */
    public function setRepoName($repo_name)
    {
        $this->repo_name = $repo_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRepoName()
    {
        return $this->repo_name;
    }


    /**
     * @return mixed
     */
    public function getUuid()
    {
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

    public function setBehatYmlArray(array $yaml)
    {
        $this->behat_yml_array = $yaml;
        return $this;
    }

    public function getBehatYmlArray()
    {
        return $this->behat_yml_array;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function setRemoteTestingServiceId($id)
    {
        $this->remoteTestingServiceId = $id;
        return $this;
    }

    public function getRemoteTestingServiceId()
    {
        return $this->remoteTestingServiceId;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = array_merge($this->tags, $tags);
        return $this;
    }

    public function getCustomData()
    {
        return $this->custom_data;
    }

    public function setCustomData(array $custom_data)
    {
        $this->custom_data = array_merge($this->custom_data, $custom_data);
        return $this;
    }
} 