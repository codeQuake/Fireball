<?php
namespace cms\data\page;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\NamedUserException;
use cms\data\content\ContentAction;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\page\menu\item\PageMenuItemEditor;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class PageAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\page\PageEditor';
    protected $permissionsDelete = array('admin.cms.page.canAddPage');
    protected $requireACP = array('delete', 'setAsHome');
   
    public function create(){
        $page = parent::create();
        PagePermissionCacheBuilder::getInstance()->reset();
        $menuItem = @unserialize($page->menuItem);
        if(isset($menuItem['has']) && $menuItem['has'] == 1){
        
            //check if has parents
            $parentItem = '';
            if($page->isChild()){
                $parent = $page->getParentPage();
                $temp = @unserialize($parent->menuItem);
                if(isset($temp['has']) && $temp['has'] ==1){
                    if($temp['id'] != 0) {
                        $parentItem = new PageMenuItem($temp['id']);
                        $parentItem = $parentItem->menuItem;
                    }
                }
            }
            
            //create
            $url = LinkHandler::getInstance()->getLink('Page', array('application' => 'cms','id' => $page->pageID, 'title' => empty($this->parameters['I18n'][1]) ? $page->title : $this->parameters['I18n'][1], 'isACP' => 0));
            $data = array('isDisabled' => 0,
                       'menuItem' => empty($page->title) ? 'cms.page.title'.$page->pageID : $page->getTitle(),
                       'menuItemLink' => $url,
                       'menuPosition' => 'header',
                       'parentMenuItem' => $parentItem,
                       'showOrder' => PageMenuItemEditor::getShowOrder(0, 'header'));
            $action = new PageMenuItemAction(array(), 'create', array('data' => $data));
            $action->executeAction();
            $returnValues = $action->getReturnValues();
            $menuItem['id'] = $returnValues['returnValues']->menuItemID;
            $menuItem = serialize($menuItem);
            $pageEditor = new PageEditor($page);
            $pageEditor->update(array('menuItem' => $menuItem));
            }
        return $page;
    }
    
    public function update(){
        parent::update();
        PagePermissionCacheBuilder::getInstance()->reset();
        
        //update menu item
        foreach($this->objectIDs as $objectID) {
            $page = new Page($objectID);
             $menuItem = @unserialize($page->menuItem);
             //update
            if(isset($menuItem['has']) && $menuItem['has'] == 1){
                if($menuItem['id'] != 0){
                    $action = new PageMenuItemAction(array($menuItem['id']), 'update', array('data' => array('menuItem' => empty($page->title) ? 'cms.page.title'.$page->pageID : $page->title,
                                                                                                             'menuItemLink' => LinkHandler::getInstance()->getLink('Page', array('application' => 'cms', 'id' => $page->pageID, 'title' => empty($this->parameters['I18n'][1]) ? $page->title : $this->parameters['I18n'][1], 'isACP' => 0)))));
                    $action->executeAction();
                }
                //create new
                else{
                    //check if has parents
                    $parentItem = '';
                    if($page->isChild()){
                        $parent = $page->getParentPage();
                        $temp = @unserialize($parent->menuItem);
                        if(isset($temp['has']) && $temp['has'] ==1){
                            if($temp['id'] != 0) {
                                $parentItem = new PageMenuItem($temp['id']);
                                $parentItem = $parentItem->menuItem;
                            }
                        }
                    }
                    $data = array('isDisabled' => 0,
                       'menuItem' => empty($page->title) ? 'cms.page.title'.$page->pageID : $page->title,
                       'menuItemLink' => LinkHandler::getInstance()->getLink('Page', array('application' => 'cms','id' => $page->pageID, 'title' => empty($this->parameters['I18n'][1]) ? $page->title : $this->parameters['I18n'][1], 'isACP' => 0)),
                       'menuPosition' => 'header',
                       'parentMenuItem' => $parentItem,
                       'showOrder' => PageMenuItemEditor::getShowOrder(0, 'header'));
                    $action = new PageMenuItemAction(array(), 'create', array('data' => $data));
                    $action->executeAction();
                    $returnValues = $action->getReturnValues();
                    $menuItem['id'] = $returnValues['returnValues']->menuItemID;
                    $menuItem = serialize($menuItem);
                    $pageEditor = new PageEditor($page);
                    $pageEditor->update(array('menuItem' => $menuItem));
                }
            }
            //delete if unchecked 
            elseif($menuItem['id'] != 0){
                $action = new PageMenuItemAction(array($menuItem['id']), 'delete', array());
                $action->executeAction();
                $menuItem['id'] = 0;
                $menuItem['has'] = 0;
                $menuItem = serialize($menuItem);
                $pageEditor = new PageEditor($page);
                $pageEditor->update(array('menuItem' => $menuItem));
            }
        }
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
        
        //delete menuItem
        $menuItem = @unserialize($page->menuItem);
        if(isset($menuItem['has']) && $menuItem['has'] == 1 && isset($menuItem['id'])){
            $action = new PageMenuItemAction(array($menuItem['id']), 'delete', array());
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
        
		else if ($this->pageEditor->isHome) {
			throw new PermissionDeniedException();
		}
        
	}
    
	public function setAsHome() {
		$this->pageEditor->setAsHome();
        
        
        //delete existing menu item
        $menuItem = @unserialize($this->pageEditor->menuItem);
        if($this->pageEditor->hasMenuItem() && $menuItem['id'] != 0){
            $action = new PageMenuItemAction(array($menuItem['id']), 'delete', array());
            $action->executeAction();
            $menuItem['id'] = 0;
            $menuItem['has'] = 0;
            $menuItem = serialize($menuItem);
            $pageEditor = new PageEditor($this->pageEditor->getDecoratedObject());
            $pageEditor->update(array('menuItem' => $menuItem));
        }
        
        //get Home Menu Item
        $sql = "SELECT menuItemID FROM wcf".WCF_N."_page_menu_item WHERE menuItemController = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute(array('cms\page\PagePage'));
        $row = $statement->fetchArray();
        $item = new PageMenuItem($row['menuItemID']);
        
        $action = new PageMenuItemAction(array($item->menuItemID), 'update', array('data' => array('menuItem' => $this->pageEditor->title)));
        $action->executeAction();
	}
}