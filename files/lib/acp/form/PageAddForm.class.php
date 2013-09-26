<?php
namespace cms\acp\form;

use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\Page;
use cms\data\page\PageList;
use wcf\system\language\I18nHandler;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

class PageAddForm extends AbstractForm{
    
    public $templateName = 'pageAdd';
    public $neededPermissions = array('admin.cms.page.canAddPage');
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';
    
    public $enableMultilangualism = true;
    
    public $title = '';
    public $description = '';
    public $metaDescription = '';
    public $metaKeywords = '';
    public $invisible = 0;
    public $robots = 'index,follow';
    public $showOrder = 0;
    public $parentID = 0;
    
    public $pageList = null;

   public function readParameters(){
        parent::readParameters();
        I18nHandler::getInstance()->register('title');
        I18nHandler::getInstance()->register('description');
        I18nHandler::getInstance()->register('metaDescription');
        I18nHandler::getInstance()->register('metaKeywords');
    }
    public function readData(){
        parent::readData();
        if(isset($_REQUEST['id'])) $this->parentID = intval($_REQUEST['id']);
        $this->pageList = new PageList();
        $this->pageList->readObjects();
        $this->pageList = $this->pageList->getObjects();
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
        if(isset($_POST['robots'])) $this->robots = StringUtil::trim($_POST['robots']);
        if(isset($_POST['parentID'])) $this->parentID = intval($_POST['parentID']);
    }
    
    public function validate(){
        parent::validate();
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
        $data = array('userID' => WCF::getUser()->userID,
                       'title' => $this->title,
                       'description' => $this->description,
                       'metaDescription' => $this->metaDescription,
                       'metaKeywords' => $this->metaKeywords,
                       'invisible' => $this->invisible,
                       'showOrder' => $this->showOrder,
                       'parentID' => $this->parentID,
                       'robots' => $this->robots);
                       
        $objectAction = new PageAction(array(), 'create', array('data' => $data));
        $objectAction->executeAction();
        $returnValues = $objectAction->getReturnValues();
        $pageID = $returnValues['returnValues']->pageID;
        $update = array();
        
        if (!I18nHandler::getInstance()->isPlainValue('title')) {
            I18nHandler::getInstance()->save('title', 'cms.page.'.$pageID.'.title', 'cms.page', PACKAGE_ID);
            $update['title'] = 'cms.page.'.$pageID.'.title';
        }
        if (!I18nHandler::getInstance()->isPlainValue('description')) {
            I18nHandler::getInstance()->save('description', 'cms.page.'.$pageID.'.description', 'cms.page', PACKAGE_ID);
            $update['description'] = 'cms.page.'.$pageID.'.description';
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
            I18nHandler::getInstance()->save('metaDescription', 'cms.page.'.$pageID.'.metaDescription', 'cms.page', PACKAGE_ID);
            $update['metaDescription'] = 'cms.page.'.$pageID.'.metaDescription';
        }
        if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
            I18nHandler::getInstance()->save('metaKeywords', 'cms.page.'.$pageID.'.metaKeywords', 'cms.page', PACKAGE_ID);
            $update['metaKeywords'] = 'cms.page.'.$pageID.'.metaKeywords';
        }
        if (!empty($update)) {
            $editor = new PageEditor($returnValues['returnValues']);
            $editor->update($update);
        }
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->title = $this->description = $this->metaDescription = $this->metaKeywords = $this->robots = '';
        $this->invisible = $this->parentID= $this->showOrder = 0;
        I18nHandler::getInstance()->reset();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        I18nHandler::getInstance()->assignVariables();
        WCF::getTPL()->assign(array('action' => 'add',
                                    'invisible' => $this->invisible,
                                    'robots' => $this->robots,
                                    'parentID' => $this->parentID,
                                    'showOrder' => $this->showOrder,
                                    'pageList' => $this->pageList));
    }
    
    
}