<?php
namespace cms\data\module;
use wcf\data\AbstractDatabaseObjectAction;

class ModuleAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\module\ModuleEditor';
    protected $permissionsDelete = array('admin.cms.content.canManageModule');
    protected $requireACP = array('delete');

}