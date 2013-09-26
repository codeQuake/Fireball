<?php
namespace cms\data\page;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;

class PageAction extends AbstractDatabaseObjectAction{

   protected $className = 'cms\data\page\PageEditor';
   protected $permissionsDelete = array('admin.cms.page.canAddPage');
   protected $requireACP = array('delete');
}