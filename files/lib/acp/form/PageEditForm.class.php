<?php
namespace cms\acp\form;
use cms\data\layout\LayoutList;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PageEditForm extends PageAddForm{
    
    
    public $pageID = 0;
    public $page = null;
    public $action = 'edit';
    
    public function readData(){
        parent::readData();
        
        //reading data
        if(isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
        $this->page = new Page($this->pageID);
        I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->page->title, 'cms.page.title\d+');
        $this->title = $this->page->title;
        I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->page->description, 'cms.page.description\d+');
        $this->description = $this->page->description;
        I18nHandler::getInstance()->setOptions('metaDescription', PACKAGE_ID, $this->page->metaDescription, 'cms.page.metaDescription\d+');
        $this->metaDescription = $this->page->metaDescription;
        I18nHandler::getInstance()->setOptions('metaKeywords', PACKAGE_ID, $this->page->metaKeywords, 'cms.page.metaKeywords\d+');
        $this->metaKeywords = $this->page->metaKeywords;
        
        
        $this->parentID = $this->page->parentID;
        $this->showOrder = $this->page->showOrder;
        $this->invisible = $this->page->invisible;
        $this->robots = $this->page->robots;
        $this->layoutID = $this->page->layoutID;
        $this->showSidebar = $this->page->showSidebar;
        $this->sidebarOrientation = $this->page->sidebarOrientation;
        $this->isCommentable = $this->page->isCommentable;
        $this->availableDuringOfflineMode = $this->page->availableDuringOfflineMode;
        $this->menuItem = @unserialize($this->page->menuItem);
        
        if(!isset($this->menuItem['has']))$this->menuItem['has'] = 0;
        $this->alias = $this->page->alias;
        
    }
    
    
    public function readFormParameters(){
        parent::readFormParameters();
          if(isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
          if(isset($_REQUEST['menuID'])) $this->menuItem['id'] = intval($_REQUEST['menuID']);
        }
    
        public function validate(){
        AbstractForm::validate();
       //validate alias
        if(empty($this->alias)) throw new UserInputException('alias', 'empty');
        if($this->parentID != 0){
            $parent = PageCache::getInstance()->getPage($this->parentID);
            if($parent->hasChildren()){
                foreach($parent->getChildren() as $child){
                    if($child->alias == $this->alias && $child->pageID != $this->pageID) throw new UserInputException('alias', 'given');
                }
            }
        }
        //1st floor ;)
        else{
            $list = new PageList();
            $list->getConditionBuilder()->add('parentID = ?', array(0));
            $list->readObjects();
            foreach($list->getObjects() as $child){
                if($child->alias == $this->alias && $child->pageID != $this->pageID) throw new UserInputException('alias', 'given');
            }
        }
        
        //check if valid
        if(preg_match('~[a-z0-9/]+(?:\-{1}[a-z0-9/]+)*~', $this->alias) !== 1) throw new UserInputException('alias', 'invalid');
        
        //validate menuitem
        $menu = @unserialize($this->page->menuItem);
        if (isset($this->menuItem['has']) && $this->menuItem['has'] == 1 && isset($menu['id']) == false && $menu['id'] != 0){
            $list = new PageMenuItemList();
            $list->readObjects();
            $list = $list->getObjects();
            foreach($list as $item){
                if(isset($this->menuItem['id']) && $this->title == $item->menuItem)
                    throw new UserInputException('menuItem', 'exists');
                if(isset($this->menuItem['id']) && $item->menuItem == 'cms.page.title'.$this->pageID);
                    throw new UserInputException('menuItem', 'exists');
            }
        }

        if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}
        $parent = new Page($this->parentID);
        if($parent === null) throw new UserInputException('parentID', 'invalid');
    }

    public function save(){
        AbstractForm::save();
        $objectAction = new PageAction(array($this->pageID), 'update', array('data' => array('alias' => $this->alias,
                                                                                            'title' => $this->title,
                                                                                           'description' => $this->description,
                                                                                           'metaDescription' => $this->metaDescription,
                                                                                           'metaKeywords' => $this->metaKeywords,
                                                                                           'invisible' => $this->invisible,
                                                                                           'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
                                                                                           'showOrder' => $this->showOrder,
                                                                                           'menuItem' => serialize($this->menuItem),
                                                                                           'parentID' => $this->parentID,
                                                                                           'layoutID' => $this->layoutID,
                                                                                           'showSidebar' => $this->showSidebar,
                                                                                           'sidebarOrientation' => $this->sidebarOrientation,
                                                                                           'robots' => $this->robots,
                                                                                           'isCommentable' => $this->isCommentable),
                                                                                           'I18n' => I18nHandler::getInstance()->getValues('title')));
        $objectAction->executeAction();
        
        $update = array();
        //save ACL
        ACLHandler::getInstance()->save($this->pageID, $this->objectTypeID);
        ACLHandler::getInstance()->disableAssignVariables();
        //update I18n
        if (!I18nHandler::getInstance()->isPlainValue('title')) {
            I18nHandler::getInstance()->save('title', 'cms.page.title'.$this->pageID, 'cms.page', PACKAGE_ID);
            $update['title'] = 'cms.page.title'.$this->pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->save('description', 'cms.page.description'.$this->pageID, 'cms.page', PACKAGE_ID);
            $update['description'] = 'cms.page.description'.$this->pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
            I18nHandler::getInstance()->save('metaDescription', 'cms.page.metaDescription'.$this->pageID, 'cms.page', PACKAGE_ID);
            $update['metaDescription'] = 'cms.page.metaDescription'.$this->pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
            I18nHandler::getInstance()->save('metaKeywords', 'cms.page.metaKeywords'.$this->pageID, 'cms.page', PACKAGE_ID);
            $update['metaKeywords'] = 'cms.page.metaKeywords'.$this->pageID;
        }
        if (!empty($update)) {
            $editor = new PageEditor(new Page($this->pageID));
            $editor->update($update);
        }
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }
    
    public function assignVariables(){
        AbstractForm::assignVariables();
        I18nHandler::getInstance()->assignVariables(!empty($_POST));
        ACLHandler::getInstance()->assignVariables($this->objectTypeID);
        WCF::getTPL()->assign(array('action' => 'edit',
                                    'objectTypeID' => $this->objectTypeID,
                                    'invisible' => $this->invisible,
                                    'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
                                    'robots' => $this->robots,
                                    'alias' => $this->alias,
                                    'parentID' => $this->parentID,
                                    'showOrder' => $this->showOrder,
                                    'pageList' => $this->pageList,
                                    'pageID' => $this->pageID,
                                    'layoutID' => $this->layoutID,
                                    'title' =>$this->title,
                                    'description' => $this->description,
                                    'metaDescription' => $this->metaDescription,
                                    'metaKeywords' => $this->metaKeywords,
                                    'menu' => $this->menuItem['has'],
                                    'showSidebar' => $this->showSidebar,
                                    'sidebarOrientation' => $this->sidebarOrientation,
                                    'menuID' => isset($this->menuItem['id']) ? $this->menuItem['id'] : 0,
                                    'page' => $this->page,
                                    'layoutList' => $this->layoutList,
                                    'isCommentable' => $this->isCommentable));
    }
}
