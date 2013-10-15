<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

class LayoutListPage extends SortablePage{
    public $objectListClassName = 'cms\data\layout\LayoutList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.layout.list';
    public $neededPermissions = array('admin.cms.style.canListLayout');
    public $templateName = 'layoutList';
    public $defaultSortfield = 'layoutID';
    public $validSortFields = array('layoutID', 'title');
}