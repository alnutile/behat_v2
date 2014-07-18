<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/18/14
 * Time: 9:40 AM
 */

namespace BehatEditor\Helpers;

use BehatEditor\Git\Exceptions\BehatEditoException;
use Symfony\Component\Filesystem\Filesystem;

class BehatFileFolderHelper {
    protected $destination = '/tmp';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem = null)
    {

        $this->filesystem = ($filesystem === null) ? new Filesystem() : $filesystem;
    }

    public function getFileSystem()
    {
        if(!$this->filesystem)
        {
            $this->setFilesystem();
        }
        return $this->filesystem;
    }

    public function setFilesystem()
    {
        $this->filesystem = new Filesystem();
        return $this->filesystem;
    }

    public static function putFileInFolder($content, $repo_name, $branch, $path, $filename)
    {
        $fileHelper = new static();
        $path = $fileHelper->getDestination() . "/$repo_name/$branch/$path/$filename";
        try {
            $fileHelper->getFileSystem()->dumpFile($path, $content);
        }
        catch(\Exception $e)
        {
            throw new BehatEditoException(sprintf("Could not make file  %s", $e->getMessage()));
        }
        return $path;
    }

    public static function removeTest($repo_name, $branch, $path, $filename)
    {
        $fileHelper = new static();
        $path = $fileHelper->getDestination() . "/$repo_name/$branch/$path/$filename";
        return $fileHelper->getFileSystem()->remove($path);
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
} 