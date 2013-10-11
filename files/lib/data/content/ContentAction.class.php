<?php
namespace cms\data\content;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use cms\data\content\section\ContentSectionAction;

class ContentAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\content\ContentEditor';
    protected $permissionsDelete = array('admin.cms.content.canAddContent');
    protected $requireACP = array('delete');
    
    public function delete(){
    
        //delete all sections beloning to the contents
        foreach($this->objectIDs as $objectID){
            $content = new Content($objectID);
            $list = $content->getSections();
            $sectionIDs = array();
            foreach($list as $section){
                $sectionIDs[] = $section->sectionID;
            }
            $action = new ContentSectionAction($sectionIDs, 'delete', array());
            $action->executeAction();
        }
        parent::delete();
    }
   
}