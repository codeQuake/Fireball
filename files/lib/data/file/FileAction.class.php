<?php
namespace cms\data\file;
use wcf\data\AbstractDatabaseObjectAction;

class FileAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\file\FileEditor';
    protected $permissionsDelete = array('admin.cms.file.canAddFile');
    protected $requireACP = array('delete');
    
    public function create(){
        parent::create();
    }
}