<?php
namespace cms\acp\page;
use wcf\page\SortablePage;

class OverviewPage extends SortablePage{
    
    public $objectListClassName = 'cms\data\page\PageList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.overview';
    public $neededPermissions = array('admin.cms.page.canListPage');
    public $templateName = 'overview';
    public $defaultSortfield = 'pageID';
    public $validSortFields = array('pageID');
    
}