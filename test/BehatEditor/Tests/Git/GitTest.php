<?php

namespace BehatEditor\Tests\Git;

use BehatEditor\BehatEditorTraits;
use BehatEditor\Helpers\BehatFileFolderHelper;
use BehatEditor\Tests\Base;
use BehatEditor\Tests\BaseTest;
use BehatEditor\Git\GithubApiWrapper;
use VCR\VCR;



class GitTest extends Base {
    const ROOT = '/../../../../';

    /**
     * @test
     * @vcr git_auth
     */
    public function authentication()
    {
        VCR::turnOn();
        VCR::insertCassette('git_auth');
        $git_client = new GithubApiWrapper();
        $git_client->authenticate();
        $this->assertNotEmpty($git_client->client->api('me')->show());
        $this->assertNotEmpty($git_client->getUsername());
        $this->assertNotEmpty($git_client->getToken());
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @expectedException \Github\Exception\RuntimeException
     * @test
     * @vcr git_auth_fail
     */
    public function authentication_fail()
    {
        VCR::turnOn();
        VCR::insertCassette('git_auth_fail');
        $git_client = new GithubApiWrapper();
        $git_client->setToken('bob');
        $this->assertNotEmpty($git_client->client->api('me')->show());
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @test
     * @vcr git_access_pass
     */
    public function check_access_pass()
    {
        VCR::turnOn();
        VCR::insertCassette('git_access_pass');
        $account = 'alnutile';
        $repo_name = 'private_test'; //private for testing
        $branch = 'master';
        $path = 'README.md';
        $response = GithubApiWrapper::checkIfHasAccess($account, $repo_name, $branch, $path);
        $this->assertNotNull($response);
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @expectedException \Exception
     * @test
     * @vcr git_access_fail
     */
    public function check_access_fail_eg_no_folder()
    {
        VCR::turnOn();
        VCR::insertCassette('git_access_fail');
        $account = 'alnutile';
        $repo_name = 'private_test'; //private for testing
        $branch = 'master';
        $path = '/foo';
        $response = GithubApiWrapper::checkIfHasAccess($account, $repo_name, $branch, $path);
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @test
     * @vcr git_show_file
     */
    public function show()
    {
        VCR::turnOn();
        VCR::insertCassette('git_show_file');
        $account = 'alnutile';
        $repo_name = 'private_test'; //private for testing
        $branch = 'master';
        $path = 'README.md';

        $gitApi = new GithubApiWrapper();
        $gitApi->authenticate();
        $gitApi->setBranch($branch)
            ->setReponame($repo_name)
            ->setAccountName($account);
        $response = $gitApi->show($path);
        $this->assertContains('private_test', $response['content'], "The file was not found");
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @expectedException Github\Exception\RuntimeException
     * @test
     * @vcr git_show_file_fail
     */
    public function show_fail()
    {
        VCR::turnOn();
        VCR::insertCassette('git_show_file_fail');
        $account = 'alnutile';
        $repo_name = 'private_test'; //private for testing
        $branch = 'master';
        $path = 'BOB.md';

        $gitApi = new GithubApiWrapper();
        $gitApi->authenticate();
        $gitApi->setBranch($branch)
            ->setReponame($repo_name)
            ->setAccountName($account);
        $gitApi->show($path);
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @test
     * @vcr git_content
     */
    public function read_content()
    {
        VCR::turnOn();
        VCR::insertCassette('git_content');
        $account = 'alnutile';
        $repo_name = 'private_test'; //private for testing
        $branch = 'master';
        $path = 'README.md';

        $gitApi = new GithubApiWrapper();
        $gitApi->authenticate();
        $gitApi->setBranch($branch)
            ->setReponame($repo_name)
            ->setAccountName($account);
        $response = $gitApi->content($path);
        $this->assertContains('private_test', $response);
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * @test
     * @vcr git_content_and_check
     */
    public function get_file_run_test()
    {
        VCR::turnOn();
        VCR::insertCassette('git_content_and_check');
        //Get the file
        $account        = 'alnutile';
        $repo_name      = 'private_test'; //private for testing
        $branch         = 'master';
        $path           = '';
        $filename       = 'test.feature';
        $gitApi         = new GithubApiWrapper();
        $gitApi->authenticate();
        $gitApi->setBranch($branch)
            ->setReponame($repo_name)
            ->setAccountName($account);


        $response = $gitApi->show($path . $filename);
        //1. get the content and make a local copy
        //   what is the path
        BehatFileFolderHelper::putFileInFolder($response['content'], $repo_name, $branch, $path, $filename);
        $this->assertFileExists("/tmp/$repo_name/$branch/$path/$filename");
        $this->assertContains('Given', file_get_contents("/tmp/$repo_name/$branch/$path/$filename"));

        BehatFileFolderHelper::removeTest($repo_name, $branch, $path, $filename);
        $this->assertFileNotExists("/tmp/$repo_name/$branch/$path/$filename");
        //2. Set that path and run that test from
        //   this will be private
        //   this will be a class

        //3. Delete the test
        VCR::eject();
        VCR::turnOff();
    }
} 