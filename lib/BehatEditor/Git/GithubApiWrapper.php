<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 5/20/14
 * Time: 12:31 PM
 */

namespace BehatEditor\Git;

use BehatEditor\Interfaces\BehatUIInterface;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Github\ResultPager;
use BehatEditor\Git\BuildFileObject;
use BehatEditor\Git\RepoSettingRepository;
use Github\Exception\RuntimeException;
use Dotenv;
\Dotenv::load(__DIR__.'/../../../');


class GithubApiWrapper implements BehatUIInterface {
    use BuildFileObject;

    /**
     * @var \Github\Client
     */
    public $client;
    protected $username;
    protected $token;
    protected $branch;
    protected $parent_file;
    protected $reponame;
    protected $folder;
    protected $logging = false;
    protected $logger;
    /**
     * @var RepoSettingRepository
     */
    private $repoSettingRepository;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        return $this;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogging($state = false)
    {
        $this->logging = $state;
        return $this;
    }

    public function getLogging()
    {
        return $this->logging;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Code from the github_api module but I did not want to
     * reply on procedural code in a class like this.
     * Yes drupal is procedural but going from that to this to
     * then a module and back to oo is too much for my brain right
     * now.
     *
     * Plus I needed to catch an exception if this fails
     *
     */
    public function authenticate()
    {
        $this->client->authenticate($this->getUsername(), $this->getToken(), Client::AUTH_HTTP_PASSWORD);
        try {
            $this->client->api('me')->show();
            return $this;
        } catch(\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception("Error logging in $message");
        }
    }

    public function __construct(Client $client = null, RepoSettingRepository $repoSettingRepository = null)
    {
        $this->client                   = ($client == null) ? new Client() : $client;
        $this->repoSettingRepository    = ($repoSettingRepository == null) ? new RepoSettingRepository() : $repoSettingRepository;
    }

    public function getRepoSettingRepository()
    {
        return $this->repoSettingRepository;
    }

    public static function checkIfHasAccess($account, $reponame, $branch, $path)
    {
        $username = $_ENV['GIT_USERNAME'];
        $token    = $_ENV['GIT_TOKEN'];
        $gitApi = new static();

        try {
            $gitApi->setUsername($username);
            $gitApi->setToken($token);
            $gitApi->authenticate();
            $gitApi->setAccountName($account)
                ->setReponame($reponame)
                ->setBranch($branch)
                ->setFolder($path);
            return $gitApi->index($path);
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new GithubWrapperExceptions("
                No permissions for $reponame for account $account in {$branch} {$error}\n");
        };
    }

    public function setReponame($reponame)
    {
        $this->reponame = $reponame;
        return $this;
    }

    public function setParentFile($parent_file)
    {
        $this->parent_file = $parent_file;
        return $this;
    }

    public function getParentFile()
    {
        return $this->parent_file;
    }

    /**
     * @return mixed
     */
    public function getReponame()
    {
        return $this->reponame;
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
        return $this;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public function setUsername($username = null)
    {
        if($username == null) {
            $username = $_ENV['GIT_USERNAME'];
        }
        $this->username = $username;
        return $this;
    }

    public function getUsername()
    {
        if(!$this->username) {
            $this->setUsername();
        }
        return $this->username;
    }

    public function setToken($token = null)
    {
        if($token === null)
        {
            $token = $_ENV['GIT_TOKEN'];
        }
        $this->token = $token;
        return $this;
    }

    public function getToken()
    {
        if(!$this->token) {
            $this->setToken();
        }
        return $this->token;
    }

    public function setAccountName($account_name)
    {
        $this->account_name = $account_name;
        return $this;
    }

    public function getAccountName()
    {
        return $this->account_name;
    }
    protected $account_name;

    public function getRateLimit()
    {
        $rate_limit = $this->client->getHttpClient()->request('/rate_limit');
        return $rate_limit->getMessage();
    }

    public function getMe()
    {
        $me = $this->client->api('me')->show();
        return $me;
    }

    public function show($path_filename)
    {
        try {
            $output =
                $this->client->api('repos')
                    ->contents()
                    ->show($this->account_name, $this->reponame, $path_filename, $this->branch);
            $output['content']  = base64_decode($output['content']);
            return $output;
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new \Exception("File Not Found $path_filename in {$this->branch} error {$error}\n");
        };
    }

    public function content($path_filename)
    {
        try {
            return
                $this->client->api('repos')
                    ->contents()
                    ->download($this->account_name, $this->reponame, $path_filename, $this->branch);
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new \Exception("File Not Found $path_filename in {$this->branch} {$error}\n");
        };
    }

    public function edit($path_filename, $content, $message, $sha, $branch){
        try {
            $output =
                $this->client->api('repos')
                    ->contents()
                    ->update($this->account_name, $this->reponame, $path_filename, $this->branch);
            return $output;
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new \Exception("File Not Found $path_filename in {$this->branch} {$error} \n");
        };
    }

    public function update($path_filename, $content, $message, $sha, $branch){
        try {
            $output =
                $this->client->api('repos')
                    ->contents()
                    ->update($this->account_name,
                        $this->reponame,
                        $path_filename,
                        $content,
                        $message,
                        $sha,
                        $branch);
            return $output;
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new \Exception("Error Saving File $path_filename in {$this->account_name} of {$this->reponame} {$this->branch} sha {$sha} error {$error}\n");
        };
    }

    public function delete($path_filename, $message, $sha){
        try {
            $output =
                $this->client->api('repos')
                    ->contents()
                    ->rm(
                        $this->account_name,
                        $this->reponame,
                        $path_filename,
                        $message,
                        $sha,
                        $this->branch);
            return $output;
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            throw new \Exception("Error Deleting File $path_filename in {$this->account_name} of {$this->reponame} {$this->branch} error {$error}\n");
        };
    }

    public function create($path_filename, $content, $message, $branch){
        try {
            $output =
                $this->client->api('repos')
                    ->contents()
                    ->create(
                        $this->account_name,
                        $this->reponame,
                        $path_filename,
                        $content,
                        $message,
                        $branch);
            return $output;
        }
        catch(\Github\Exception\ErrorException $e) {
            $error = $e->getMessage();
            if($this->logging)
            {
                $this->getLogger()->log(sprintf("Error File repo path %s, error %s", $path_filename, $error));
            }
            throw new \Exception("Error Creating File $path_filename in {$this->account_name} of {$this->reponame} {$this->branch} error {$error}\n");
        };
    }

    public function run(){}

    public function index($path_filename){
        $message = "account {$this->account_name} path {$path_filename} repo {$this->reponame} branch {$this->branch} gituser  {$this->getUsername()}";
        try {

            if($this->logging)
            {
                $this->getLogger()->log(sprintf("Index  %s", $message));
            }
            return
                $this->client->api('repos')
                    ->contents()
                    ->show($this->account_name, $this->reponame, $path_filename, $this->branch);
        }
        catch(\Exception $e) {
            $error = $e->getMessage();
            if($this->logging)  {
                $this->getLogger()->log(sprintf("Index  %s", $error));
            }
            throw new \Exception("Github Error for user {$this->getAccountName()}
                in path $path_filename in repo {$this->reponame} branch {$this->branch} error {$error}\n");
        };
    }


    public function commits($username, $repo_name, $path, $since, $until, $branch){
        $message = "Getting commit count for repo {$this->reponame}";
        try {
            $params = [
                'path'  => $path,
                'since' => $since,
                'until' => $until,
                'sha'   => $branch
            ];
            return
                $this->client->api('repos')
                    ->commits()
                    ->all($username, $repo_name, $params);
        }
        catch(\Exception $e) {
            $error = $e->getMessage();
            if($this->logging)
            {
                $this->getLogger()->log(sprintf("Api activity %s", $message));
            }
            throw new \Exception("GitHub Error for user {$this->getAccountName()}
                in repo {$this->reponame} branch {$this->branch} error {$error}\n");
        };
    }

    public function tokens($path)
    {
        try {
            $files =
                $this->client->api('repos')
                    ->contents()
                    ->show($this->account_name, $this->reponame, $path, $this->branch);
            if($this->logging) {
                $this->getLogger()->log(sprintf("Token files found %s", $files));
            }
            $files = $this->iterateOverTokens($files);

            return $files;
        }
        catch(\Github\Exception\RuntimeException $e) {
            $error = $e->getMessage();
            throw new \Exception("Folder Not Found $path in {$this->branch} $error \n");
        };
    }

    protected function iterateOverTokens($files)
    {
        $files_for_this_token = array();
        $parent_prefix = substr($this->parent_file, 0, -8);
        foreach($files as $value) {
            if(strpos($value['name'], $parent_prefix) !== false) {
                $get_file_info =
                    $this->client->api('repos')
                        ->contents()
                        ->show($this->account_name, $this->reponame, $value['path'], $this->branch);
                $get_file_info['content'] = base64_decode($get_file_info['content']);
                $files_for_this_token[] = $get_file_info;
            }
        }
        return $files_for_this_token;
    }

    public function setupFolder()
    {
        $message = "account {$this->account_name} path {$this->folder} repo {$this->reponame} branch {$this->branch} gituser  {$this->getUsername()}";
        try {
            if($this->logging) {
                $this->getLogger()->log(sprintf("Api setup folder %s", $message));
            }
            $check_folder =
                $this->client->api('repos')
                    ->contents()
                    ->show($this->account_name, $this->reponame, $this->folder, $this->branch);
            return TRUE;
        }
        catch(\Exception $e) {
            $message = "Setting up new folder for tests";
            $this->create($this->folder . '/readme.md', $this->defaultReadme(), $message, $this->branch);
        };
    }

    protected function defaultReadme()
    {
        $readme = "Your tests for behat will be in this folder";
        return $readme;
    }
}