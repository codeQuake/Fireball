<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

class StylesheetListPage extends SortablePage{
    public $objectListClassName = 'cms\data\stylesheet\StylesheetList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.stylesheet.list';
    public $neededPermissions = array('admin.cms.style.canListStylesheet');
    public $templateName = 'stylesheetList';
    public $defaultSortfield = 'sheetID';
    public $validSortFields = array('sheetID', 'title');
}