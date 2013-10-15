<?php
namespace cms\data\page;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use cms\data\content\ContentAction;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\system\WCF;

class PageAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\page\PageEditor';
    protected $permissionsDelete = array('admin.cms.page.canAddPage');
    protected $requireACP = array('delete', 'setAsHome');
   
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
    
    
	public function validateSetAsHome() {
		WCF::getSession()->checkPermissions(array('admin.cms.page.canAddPage'));
		
		$this->pageEditor = $this->getSingleObject();
		if (!$this->pageEditor->pageID) {
			throw new UserInputException('objectIDs');
		}
		else if ($this->pageEditor->isPrimary) {
			throw new PermissionDeniedException();
		}
	}
    
	public function setAsHome() {
		$this->pageEditor->setAsHome();
        $data = array('isDisabled' => 0,
                       'menuItem' => $this->pageEditor->title,
                       'menuItemController' => 'cms\page\PagePage',
                       'menuPosition' => 'header',
                       'parentMenuItem' => '',
                       'showOrder' => 1);
        $action = new PageMenuItemAction(array(), 'create', array('data' => $data));
        $action->executeAction();
	}
}