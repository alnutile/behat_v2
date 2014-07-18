<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/18/14
 * Time: 8:40 AM
 */

namespace BehatEditor\Git;


class RepoSettingRepository {

    /**
     * @var RepoSettingModel
     */
    private $repoSettingModel;

    public function __construct(RepoSettingModel $repoSettingModel = null)
    {

        $this->repoSettingModel = ($repoSettingModel == null) ? new RepoSettingModel() : $repoSettingModel;
    }


}