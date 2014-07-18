<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/18/14
 * Time: 10:26 AM
 */

namespace BehatEditor\Controllers;
use BehatEditor\BehatEditorApp;
use BehatEditor\BehatEditorBehatWrapper;
use BehatEditor\Git\Exceptions\GithubWrapperExceptions;
use BehatEditor\Git\GithubApiWrapper;
use BehatEditor\Helpers\BehatFileFolderHelper;
use BehatWrapper\BehatCommand;

/**
 * @TODO break this out into in a Service
 * and to an interface then extend it with the
 * github flow needed for this project
 *
 * Class BehatRunController
 * @package BehatEditor\Controllers
 */
class BehatRunController {

    protected $content;
    protected $filename;

    protected $repo_settings = [
        'repo_name' => null,
        'branch'    => null,
        'account'   => null,
        'folder'    => null
    ];
    protected $repo_setting_id;
    /**
     * @var GithubApiWrapper
     */
    private $githubApiWrapper;
    /**
     * @var BehatEditorBehatWrapper
     */
    private $behatEditorBehatWrapper;

    /**
     * @var BehatCommand
     */
    private $behatCommand;
    /**
     * @var \BehatEditor\BehatEditorApp
     */
    private $behatEditorApp;

    public function __construct(GithubApiWrapper $githubApiWrapper = null,
                                BehatEditorApp $behatEditorApp)
    {
        $this->githubApiWrapper = $githubApiWrapper;
        $this->behatEditorApp = $behatEditorApp;
    }

    public function run($repo_setting_id, $filename)
    {
        $this->setFilename($filename);
        $this->setRepoSettingId($repo_setting_id);
        //1. Get the file from Github
        $this->getFileFromGithub($filename);
        //2. Put the file to where we need it
        $path = BehatFileFolderHelper::putFileInFolder($this->content, $this->repo_settings['repo_name'], $this->repo_settings['branch'], $this->repo_settings['folder'], $this->filename);
        //   now set it on the command method
        $this->behatEditorApp->getBehatCommand()->setTestPath($path);
        //4. Run the test against that file
        $output = $this->behatEditorApp->getBehatWrapper()->run($this->behatEditorApp->getBehatCommand());

        //5. Clean up
        BehatFileFolderHelper::removeTest($this->repo_settings['repo_name'], $this->repo_settings['branch'], $this->repo_settings['folder'], $this->filename);
        return $output;
    }

    public function setRepoSettingId($repo_setting_id)
    {
        $this->repo_setting_id = $repo_setting_id;
        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    protected function getFileFromGithub()
    {
        $this->getRepoSettings();
        $this->getGithubApiWrapper()->authenticate();
        $this->getGithubApiWrapper()
            ->setBranch($this->repo_settings['branch'])
            ->setReponame($this->repo_settings['repo_name'])
            ->setAccountName($this->repo_settings['account']);
        try {
            $path = (strlen($this->repo_settings['folder'])) ? $this->repo_settings['folder'] . '/' : '';
            $this->content = $this->getGithubApiWrapper()->content($path . $this->filename);
            return $this->content;
        }
        catch(\Exception $e)
        {
            throw new GithubWrapperExceptions(sprintf("Error getting file %s", $e->getMessage()));
        }

    }

    protected function getRepoSettings()
    {

        //1 Query the values using $this->repo_setting_id
        //2 Set the params for later use
        //3 Throw exception if not found
        $this->repo_settings = [
            'repo_name' => 'private_test',
            'branch'    => 'master',
            'account'   => 'alnutile',
            'folder'    => ''
        ];
        return $this->repo_settings;
    }

    public function getGithubApiWrapper()
    {
        if($this->githubApiWrapper == null) {
            $this->setGithubApiWrapper();
        }
        return $this->githubApiWrapper;
    }

    public function setGithubApiWrapper()
    {
        $this->githubApiWrapper = new GithubApiWrapper();
        return $this;
    }

} 