<?php

namespace templates\art_projects;

use Lcms\Articles\Group as ArticleGroup;

/**
 * Notice: This code is generated when article types have been changed.
 * @package templates\art_projects
 * @method int getGroup_id
 * @method int getParent_id
 * @method string getGroup_name
 * @method Group[] Children
 */
class GroupData extends ArticleGroup
{
    public function __construct(array $data, array $addon)
    {
        $this->setTypeCollection(['group_id' => 'int', 'parent_id' => 'int', 'group_name' => 'string', ]);
        parent::__construct($data, $addon);
    }
}