<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/18/14
 * Time: 7:43 AM
 */

namespace BehatEditor\Git;


trait BuildFileObject {
    protected $tags;
    protected $fileObject;

    public function _tags_array($file) {
        $file_to_array = self::_turn_file_to_array($file);
        $tags = array();
        foreach($file_to_array as $key => $value) {
            if(strpos($value, '@') !== FALSE && !strpos($value, '"')) {
                foreach(explode(' ', $value) as $tag) {
                    if(!empty($tag)) {
                        $tags[] = trim($tag);
                    }
                }
            }
        }
        return $tags;
    }

    protected function _turn_file_to_array($file) {
        $array = explode("\n", $file);
        foreach($array as $key => $value) {
            if(strlen($value) <= 1) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public function getFileObject()
    {
        return $this->fileObject;
    }

    public function setFileObject($values)
    {
        $this->fileObject = $this->buildFileObject($values);
    }


    public function buildFileObject($value)
    {
        $additions = array(
            'tags_array'      => $this->_tags_array($value['content']),
            'contents'        => $value['content'],
            'git_name'        => $value['name'],
            'scenario'        => $value['content'], //legacy
            'filename_no_ext' => $this->filename_no_ext($value['name']),
        );
        unset($value['name']);
        return array_merge($value, $additions);
    }

    protected function filename_no_ext($filename)
    {
        return substr($filename, 0, -8);
    }
} 