<?php
namespace cms\acp\form;
use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\Page;
use cms\data\page\PageCache;
use cms\data\page\PageList;
use cms\data\layout\LayoutList;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\system\language\I18nHandler;
use wcf\system\acl\ACLHandler;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 **/

class PageAddForm extends AbstractForm{
    
    public $templateName = 'pageAdd';
    public $neededPermissions = array('admin.cms.page.canAddPage');
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';
    public $objectTypeID = 0;
    
    public $enableMultilangualism = true;
    public $pageID = 0;
    public $action = 'add';
    public $title = '';
    public $alias = '';
    public $description = '';
    public $metaDescription = '';
    public $metaKeywords = '';
    public $invisible = 0;
    public $availableDuringOfflineMode = 0;
    public $robots = 'index,follow';
    public $showSidebar = 0;
    public $sidebarOrientation = 'right';
    public $showOrder = 0;
    public $parentID = 0;
    public $menuItem = array();
    public $pageList = null;
    public $layoutList = null;
    public $layoutID = 0;
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
        if(!isset($this->menuItem['has']))$this->menuItem['has'] = 0;
        if(isset($_REQUEST['id'])) $this->parentID = intval($_REQUEST['id']);
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
        if(isset($_POST['alias'])) $this->alias = StringUtil::trim($_POST['alias']);
        if(isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
        if(isset($_POST['availableDuringOfflineMode'])) $this->availableDuringOfflineMode = intval($_POST['availableDuringOfflineMode']);
        if(isset($_POST['invisible'])) $this->invisible = intval($_POST['invisible']);
        if(isset($_POST['menuItem'])) $this->menuItem['has'] = intval($_POST['menuItem']);
        if(isset($_POST['robots'])) $this->robots = StringUtil::trim($_POST['robots']);
        if(isset($_POST['parentID'])) $this->parentID = intval($_POST['parentID']);
        if(isset($_POST['layoutID'])) $this->layoutID = intval($_POST['layoutID']);
        if(isset($_POST['showSidebar'])) $this->showSidebar = intval($_POST['showSidebar']);
        if(isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);
        if(isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
    }
    
    public function validate(){
        parent::validate();
        //validate alias
        if(empty($this->alias)) throw new UserInputException('alias', 'empty');
        if($this->parentID != 0){
            $parent = PageCache::getInstance()->getPage($this->parentID);
            if($parent->hasChildren()){
                foreach($parent->getChildren() as $child){
                    if($child->alias == $this->alias && $this->action == 'add') throw new UserInputException('alias', 'given');
                    else{
                        if($child->alias == $this->alias && $child->pageID != $this->pageID) throw new UserInputException('alias', 'given');
                    }
                }
            }
        }
        //1st floor ;)
        else{
            $list = new PageList();
            $list->getConditionBuilder()->add('parentID = ?', array(0));
            $list->readObjects();
            foreach($list->getObjects() as $child){
                if($child->alias == $this->alias && $this->action == 'add') throw new UserInputException('alias', 'given');
                    else{
                        if($child->alias == $this->alias && $child->pageID != $this->pageID) throw new UserInputException('alias', 'given');
                    }
            }
        }
        
        //check if valid
        if(preg_match('~[a-z0-9/]+(?:\-{1}[a-z0-9/]+)*~', $this->alias) !== 1) throw new UserInputException('alias', 'invalid');
        
        //validate menuitem
        $list = new PageMenuItemList();
        $list->readObjects();
        $list = $list->getObjects();
        foreach($list as $item){
            if(isset($this->menuItem) && $this->title == $item->menuItem)
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
        $page = new Page($this->parentID);
        if($page === null) throw new UserInputException('parentID', 'invalid');
    }
    
    public function save(){
        parent::save();
        $data = array('alias' => $this->alias,
                        'title' => $this->title,
                       'description' => $this->description,
                       'metaDescription' => $this->metaDescription,
                       'metaKeywords' => $this->metaKeywords,
                       'invisible' => $this->invisible,
                       'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
                       'menuItem' => serialize($this->menuItem),
                       'showOrder' => $this->showOrder,
                       'layoutID' => $this->layoutID,
                       'parentID' => $this->parentID,
                       'showSidebar' => $this->showSidebar,
                       'sidebarOrientation' => $this->sidebarOrientation,
                       'robots' => $this->robots,
                       'isCommentable' => $this->isCommentable);
                       
        $objectAction = new PageAction(array(), 'create', array('data' => $data, 'I18n' => I18nHandler::getInstance()->getValues('title')));
        $objectAction->executeAction();
        
        $returnValues = $objectAction->getReturnValues();
        $pageID = $returnValues['returnValues']->pageID;
        //save ACL
        ACLHandler::getInstance()->save($pageID, $this->objectTypeID);
        ACLHandler::getInstance()->disableAssignVariables();
        //update I18n
        $update = array();
        
        if (!I18nHandler::getInstance()->isPlainValue('title')) {
            I18nHandler::getInstance()->save('title', 'cms.page.title'.$pageID, 'cms.page', PACKAGE_ID);
            $update['title'] = 'cms.page.title'.$pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->save('description', 'cms.page.description'.$pageID, 'cms.page', PACKAGE_ID);
            $update['description'] = 'cms.page.description'.$pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
            I18nHandler::getInstance()->save('metaDescription', 'cms.page.metaDescription'.$pageID, 'cms.page', PACKAGE_ID);
            $update['metaDescription'] = 'cms.page.metaDescription'.$pageID;
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
            I18nHandler::getInstance()->save('metaKeywords', 'cms.page.metaKeywords'.$pageID, 'cms.page', PACKAGE_ID);
            $update['metaKeywords'] = 'cms.page.metaKeywords'.$pageID;
        }
        if (!empty($update)) {
            $editor = new PageEditor($returnValues['returnValues']);
            $editor->update($update);
        }
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
        $this->title = $this->description = $this->metaDescription = $this->metaKeywords = $this->robots = $this->alias = '';
        $this->sidebarOrientation = 'right';
        $this->invisible = $this->parentID= $this->showOrder = $this->showSidebar = 0;
        $this->menuItem = array();
        I18nHandler::getInstance()->reset();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        I18nHandler::getInstance()->assignVariables();
        ACLHandler::getInstance()->assignVariables($this->objectTypeID);
        WCF::getTPL()->assign(array('action' => 'add',
                                    'objectTypeID' => $this->objectTypeID,
                                    'invisible' => $this->invisible,
                                    'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
                                    'robots' => $this->robots,
                                    'alias' => $this->alias,
                                    'parentID' => $this->parentID,
                                    'showOrder' => $this->showOrder,
                                    'menu' => $this->menuItem['has'],
                                    'layoutID' => $this->layoutID,
                                    'showSidebar' => $this->showSidebar,
                                    'sidebarOrientation' => $this->sidebarOrientation,
                                    'pageList' => $this->pageList,
                                    'layoutList' => $this->layoutList,
                                    'isCommentable' => $this->isCommentable));
    }
    
    
}