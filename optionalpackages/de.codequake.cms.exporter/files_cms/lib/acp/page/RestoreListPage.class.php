<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

class RestoreListPage extends SortablePage {
    public $objectListClassName = 'cms\data\restore\RestoreList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.restore.list';
    public $neededPermissions = array(
        'admin.cms.restore.canRestore'
    );
    public $templateName = 'restoreList';
    public $defaultSortField = 'time';
    public $defaultSortOrder = 'DESC';
    public $validSortFields = array(
        'time'
    );
}
