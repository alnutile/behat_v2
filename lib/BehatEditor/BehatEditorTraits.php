<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 2:59 PM
 */

namespace BehatEditor;
use Rhumsaa\Uuid\Uuid;

trait BehatEditorTraits {

    protected $uuid;
    protected $status;

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

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

} 