<?php
namespace cms\acp\form;
use wcf\system\language\I18nHandler;
use wcf\form\AbstractForm;
use cms\data\content\section\ContentSectionEditor;
use cms\data\content\section\ContentSectionAction;
use wcf\system\event\EventHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\StringUtil;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

class ContentSectionAddForm extends AbstractForm{

    public $templateName = 'sectionAdd';
    public $neededPermissions = array('admin.cms.content.canAddContentSection');
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.add';
    public $enableMultilangualism = true;
    
    public $objectType = null;
    public $objectTypeList = array();
    public $objectTypeProcessor = null;
    
    public $contentID = 0;
    public $showOrder = 0;
    public $cssID = '';
    public $cssClasses = '';
    public $send = false;
    
    public function readParameters(){
        I18nHandler::getInstance()->register('sectionData');
         //getObjectTypeByName($definitionName, $objectTypeName);
        if(isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        $this->objectTypeProcessor = $this->objectType->getProcessor();
    }

    public function readData(){
        parent::readData();
        $this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.section.type');
        if(isset($_GET['id'])) $this->contentID = intval($_GET['id']);
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        $this->objectTypeProcessor = $this->objectType->getProcessor();
        if(isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
        if(isset($_POST['id'])) $this->contentID = intval($_POST['id']);
        if(isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
        if(isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
        if(isset($_POST['cssID'])) $this->cssID = StringUtil::trim($_POST['cssID']);
    }
    
    public function validate(){
        parent::validate();
        $this->objectTypeProcessor->validateFormData();
    }
    
    public function submit() {
		// call submit event
		EventHandler::getInstance()->fireAction($this, 'submit');

		$this->readFormParameters();

		try {
			// send message or save as draft
			if ($this->send) {
                $this->objectTypeProcessor->readFormData();
				$this->validate();
				// no errors
				$this->save();
			}
		}
		catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
    
    public function save(){
        parent::save();
        $data = array('contentID' => $this->contentID,
                    'showOrder' => $this->showOrder,
                    'cssID' => $this->cssID,
                    'cssClasses' => $this->cssClasses,
                    'sectionTypeID' => $this->objectType->objectTypeID,
                    'sectionData' => $this->objectTypeProcessor->getFormData()['sectionData']);
        $objectAction = new ContentSectionAction(array(), 'create', array('data' => $data));
        $objectAction->executeAction();
        $returnValues = $objectAction->getReturnValues();
        
        $sectionID = $returnValues['returnValues']->sectionID;
        $this->objectTypeProcessor->saveFormData();
        if($this->objectTypeProcessor->isMultilingual){
            $update = array();
            if (!I18nHandler::getInstance()->isPlainValue('sectionData')) {
                I18nHandler::getInstance()->save('sectionData', 'cms.content.section.'.$sectionID.'.sectionData', 'cms.content.section', PACKAGE_ID);
                $update['sectionData'] = 'cms.content.section.'.$sectionID.'.sectionData';
            }
            if (!empty($update)) {
                $editor = new ContentSectionEditor($returnValues['returnValues']);
                $editor->update($update);
            }
        }
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        I18nHandler::getInstance()->assignVariables();
        
        if($this->objectType != null) $this->objectTypeProcessor->assignFormVariables();
        
        WCF::getTPL()->assign(array('action' => 'add',
                                    'cssID' => $this->cssID,
                                    'cssClasses' => $this->cssClasses,
                                    'contentID' => $this->contentID,
                                    'showOrder' => $this->showOrder,
                                    'objectTypeName' => isset($this->objectType->objectType) ? $this->objectType->objectType : '',
                                    'objectType' => isset($this->objectType) ? $this->objectType : null,
                                    'objectTypeList' => $this->objectTypeList));
    }
}