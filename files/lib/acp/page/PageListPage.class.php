<?php
namespace cms\acp\page;
use cms\data\page\PageList;
use wcf\page\SortablePage;

class PageListPage extends SortablePage{
    
    public $objectListClassName = 'cms\data\page\PageList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';
    public $neededPermissions = array('admin.cms.page.canListPage');
    public $templateName = 'pageList';
    public $defaultSortfield = 'showOrder';
    public $validSortFields = array('pageID', 'title', 'showOrder');
    public $objectList = array();
    
    public function initObjectList(){
        $this->objectList = new PageList();
        //get the roots
        $this->objectList->getConditionBuilder()->add('page.contentType = ?', array(0));
    }
}