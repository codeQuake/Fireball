<?php
namespace cms\data\stylesheet;
use wcf\data\AbstractDatabaseObjectAction;

class StylesheetAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\stylesheet\StylesheetEditor';
    protected $permissionsDelete = array('admin.cms.stylesheet.canAddStylesheet');
    protected $requireACP = array('delete');
}