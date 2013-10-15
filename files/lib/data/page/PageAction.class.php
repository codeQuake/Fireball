<?php
namespace cms\data\page;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use cms\data\content\ContentAction;
use cms\system\cache\builder\PagePermissionCacheBuilder;

class PageAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\page\PageEditor';
    protected $permissionsDelete = array('admin.cms.page.canAddPage');
    protected $requireACP = array('delete');
   
    public function create(){
        $page = parent::create();
        PagePermissionCacheBuilder::getInstance()->reset();
        return $page;
    }
    
    public function update(){
        parent::update();
        PagePermissionCacheBuilder::getInstance()->reset();
    }
    
    public function delete(){
    
        //delete all contents beloning to the pages
        foreach($this->objectIDs as $objectID){
            $page = new Page($objectID);
            $list = $page->getContentList();
            $contentIDs = array();
            foreach($list as $content){
                $contentIDs[] = $content->contentID;
            }
            $action = new ContentAction($contentIDs, 'delete', array());
            $action->executeAction();
        }
        parent::delete();
    }
}