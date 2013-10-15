<?php
namespace cms\data\layout;
use wcf\data\AbstractDatabaseObjectAction;

class LayoutAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\layout\LayoutEditor';
    protected $permissionsDelete = array('admin.cms.layout.canAddLayout');
    protected $requireACP = array('delete');
    
    public function create(){
        parent::create();
    }
}