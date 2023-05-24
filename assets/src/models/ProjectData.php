<?php

namespace templates\art_projects;

use Lcms\Articles\SubType;
use Lcms\Articles\Table;

/**
 * Notice: This code is generated when article types have been changed.
 * @package templates\art_projects
 * @method string getNaam
 * @method File getBestand
 * @method string getUrl
 * @method string getDesign_id
 * @method string getDesign_type
 * @method int getArtikel_id
 * @method int getGroup_id
 * @method int getHide
 * @method int getParent
 * @method int getShortcut
 * @method int getGewicht
 * @method int getUser_author
 * @method \Lcms\Utils\DateTime getCreate_time
 * @method array subTypes(int $limit = 10, int $page = null)
 */
class ProjectData extends Table
{
    public function subTypeFactory(array $data) : SubType
    {
        return $data;
    }

    public function __construct(array $data, string $tableName)
    {
        $this->setModuleId(1);
        $this->setTypeCollection(['naam' => 'string', 'bestand' => 'File', 'url' => 'string', 'design_id' => 'string', 'design_type' => 'string', 'artikel_id' => 'int', 'group_id' => 'int', 'hide' => 'int', 'parent' => 'int', 'shortcut' => 'int', 'gewicht' => 'int', 'user_author' => 'int', 'create_time' => '\Lcms\Utils\DateTime', ]);
        $this->setSTTN($tableName);
        $this->acquireTableData($data);
    }
}