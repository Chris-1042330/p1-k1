<?php

namespace templates\art_projects;

use Lcms\Articles\Articles;
use Lcms\Articles\Group as ArticleGroup;
use Lcms\Articles\Table;

/**
 * Notice: This code is generated when article types have been changed.
 * @package templates\art_projects
 * @method Project[] getAll(int $limit = 100, int $page = null)
 * @method Project getById(int $id)
 * @method GroupMap getGroups
 */
class ProjectModel extends Articles
{
    public function __construct()
    {
        parent::__construct('1', '1');
    }

    public function groupFactory(array $data, array $addon) : ArticleGroup
    {
        return new my\Group($data, $addon);
    }

    public function dataFactory(array $data, string $sttn) : Table
    {
        return new my\Project($data, $sttn);
    }
}