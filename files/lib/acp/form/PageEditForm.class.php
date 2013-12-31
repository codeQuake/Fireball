<?php
namespace cms\acp\form;

use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\Page;
use cms\data\page\PageList;
use cms\data\layout\LayoutList;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\system\language\I18nHandler;
use wcf\system\acl\ACLHandler;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

class PageEditForm extends AbstractForm{
    
    public $templateName = 'pageAdd';
    public $neededPermissions = array('admin.cms.page.canAddPage');
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';
    public $objectTypeID = 0;
    public $enableMultilangualism = true;
    
    public $title = '';
    public $description = '';
    public $metaDescription = '';
    public $showSidebar = 0;
    public $sidebarOrientation = 'right';
    public $metaKeywords = '';
    public $invisible = 0;
    public $layoutID = 0;
    public $robots = 'index,follow';
    public $showOrder = 0;
    public $menuItem = array();
    public $pageID = 0;
    public $page = null;
    public $pageList = null;
    public $layoutList = null;
    public $isCommentable = 0;

   public function readParameters(){
        parent::readParameters();
        I18nHandler::getInstance()->register('title');
        I18nHandler::getInstance()->register('description');
        I18nHandler::getInstance()->register('metaDescription');
        I18nHandler::getInstance()->register('metaKeywords');
        $this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.page');
    }
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
        $this->menuItem = @unserialize($this->page->menuItem);
        if(!isset($this->menuItem['has'])) $this->menuItem['has'] = 0;
        
        $this->pageList = new PageList();
        $this->pageList->readObjects();
        $this->pageList = $this->pageList->getObjects();
        
        $this->layoutList = new LayoutList();
        $this->layoutList->readObjects();
        $this->layoutList = $this->layoutList->getObjects();
    }
    public function readFormParameters(){
        parent::readFormParameters();
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
        if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
        if (I18nHandler::getInstance()->isPlainValue('metaDescription')) $this->metaDescription = StringUtil::trim(I18nHandler::getInstance()->getValue('metaDescription'));
        if (I18nHandler::getInstance()->isPlainValue('metaKeywords')) $this->metaKeywords = StringUtil::trim(I18nHandler::getInstance()->getValue('metaKeywords'));
        if(isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
        if(isset($_POST['invisible'])) $this->invisible = intval($_POST['invisible']);
        if(isset($_POST['menuItem'])) $this->menuItem['has'] = intval($_POST['menuItem']);
        if(isset($_POST['robots'])) $this->robots = StringUtil::trim($_POST['robots']);
        if(isset($_POST['parentID'])) $this->parentID = intval($_POST['parentID']);
        if(isset($_POST['layoutID'])) $this->layoutID = intval($_POST['layoutID']);
        if(isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
        if(isset($_REQUEST['menuID'])) $this->menuItem['id'] = intval($_REQUEST['menuID']);
        if(isset($_POST['showSidebar'])) $this->showSidebar = intval($_POST['showSidebar']);        
        if(isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);        
        if(isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
    }
    
    public function validate(){
        parent::validate();
        
        //validate menuitem
        $list = new PageMenuItemList();
        $list->readObjects();
        $list = $list->getObjects();
        foreach($list as $item){
            if(isset($this->menuItem) && $this->title == $item->menuItem)
                throw new UserInputException('menuItem', 'exists');
            if(isset($this->menuItem) && $item->menuItem == 'cms.page.title'.$this->pageID);
                throw new UserInputException('menuItem', 'exists');
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
        parent::save();
        $objectAction = new PageAction(array($this->pageID), 'update', array('data' => array('title' => $this->title,
                                                                                           'description' => $this->description,
                                                                                           'metaDescription' => $this->metaDescription,
                                                                                           'metaKeywords' => $this->metaKeywords,
                                                                                           'invisible' => $this->invisible,
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
            $update['metaDescription'] = 'cms.page..metaDescription'.$this->pageID;
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
        parent::assignVariables();
        I18nHandler::getInstance()->assignVariables(!empty($_POST));
        ACLHandler::getInstance()->assignVariables($this->objectTypeID);
        WCF::getTPL()->assign(array('action' => 'edit',
                                    'objectTypeID' => $this->objectTypeID,
                                    'invisible' => $this->invisible,
                                    'robots' => $this->robots,
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