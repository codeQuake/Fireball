<?php
namespace cms\data\content\section;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;

class ContentSectionAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\content\section\ContentSectionEditor';
    protected $permissionsDelete = array('admin.cms.content.canAddContentSection');
    protected $requireACP = array('delete');
}