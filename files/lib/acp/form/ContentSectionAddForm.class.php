<?php
namespace cms\acp\form;
use wcf\system\language\I18nHandler;
use wcf\form\AbstractForm;
use cms\data\content\section\ContentSectionAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\StringUtil;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

class ContentSectionAddForm extends AbstractForm{

    public $templateName = 'sectionAdd';
    public $neededPermissions = array('admin.cms.page.canAddContent');
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.add';
    public $enableMultilangualism = true;
    
    public $objectType = null;
    public $contentID = 0;
    public $showOrder = 0;
    public $cssID = '';
    public $cssClasses = '';
    
    public function readParameters(){
        I18nHandler::getInstance()->register('sectionData');
         //getObjectTypeByName($definitionName, $objectTypeName);
        if(isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        
    }

    public function readData(){
        parent::readData();
       if(isset($_REQUEST['contentID'])) $this->contentID = intval($_REQUEST['contentID']);
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        $this->objectType->getProcessor()->readFormData();
        
        if(isset($_REQUEST['contentID'])) $this->contentID = intval($_REQUEST['contentID']);
        if(isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
        if(isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
        if(isset($_POST['cssID'])) $this->cssID = StringUtil::trim($_POST['cssID']);
    }
    
    public function validate(){
        parent::validate();
        $this->objectType->getProcessor()->validateFormData();
    }
    
    public function save(){
        parent::save();
        $data = array('contentID' => $this->contentID,
                    'showOrder' => $this->showOrder,
                    'cssID' => $this->cssID,
                    'cssClasses' => $this->cssClasses,
                    'sectionTypeID' => $this->objectType->objectTypeID,
                    'sectionData' => $this->objectType->getProcessor()->formData['sectionData']);
        $objectAction = new ContentSectionAction(array(), 'create', array('data' => $data));
        $objectAction->executeAction();
        $returnValues = $objectAction->getReturnValues();
        
        $this->objectType->getProcessor()->saveFormData();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        I18nHandler::getInstance()->assignVariables();
        
        if($this->objectType != null) $this->objectType->getProcessor()->assignFormVariables();
        
        WCF::getTPL()->assign(array('action' => 'add',
                                    'cssID' => $this->cssID,
                                    'cssClasses' => $this->cssClasses,
                                    'contentID' => $this->contentID,
                                    'showOrder' => $this->showOrder,
                                    'objectType' => isset($this->objectType) ? $this->objectType : ''));
    }
}