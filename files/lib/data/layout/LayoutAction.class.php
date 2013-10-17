<?php
namespace cms\data\layout;
use wcf\data\AbstractDatabaseObjectAction;
use cms\system\layout\LayoutHandler;

class LayoutAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\layout\LayoutEditor';
    protected $permissionsDelete = array('admin.cms.style.canAddLayout');
    protected $requireACP = array('delete');
    

    public function delete(){
        //delete less files
        foreach($this->objectIDs as $objectID){
            LayoutHandler::getInstance()->deleteStylesheet($objectID);
        }
        parent::delete();
    }
}