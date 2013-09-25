<?php
namespace cms\acp\page;
use cms\data\content\ContentList;
use wcf\page\SortablePage;

class ContentListPage extends SortablePage{
    
    public $objectListClassName = 'cms\data\content\ContentList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.list';
    public $neededPermissions = array('admin.cms.content.canListContent');
    public $templateName = 'contentList';
    public $defaultSortfield = 'contentID';
    public $validSortFields = array('contentID', 'title');
    public $objectList = array();
    
}