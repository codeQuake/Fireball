<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

class ModuleListPage extends SortablePage{
    public $objectListClassName = 'cms\data\module\ModuleList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.module.list';
    public $neededPermissions = array('admin.cms.content.canManageModule');
    public $templateName = 'moduleList';
    public $defaultSortfield = 'moduleID';
    public $validSortFields = array('moduleID', 'moduleTitle');
}