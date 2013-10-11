<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use cms\data\content\section\ContentSectionEditor;
use wcf\util\MessageUtil;
use wcf\system\bbcode\BBCodeHandler;
use wcf\data\smiley\SmileyCache;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use cms\data\content\section\ContentSection;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;

class TextContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.text';
    public $isMultilingual = true;
    
    //message options
    public $attachmentHandler = null;
    public $defaultSmilies = array();
    public $smileyCategories = array();
    public $allowedBBCodesPermission = 'user.message.allowedBBCodes';
    public $enableBBCodes = 1;
    public $enableHtml = 0;
    public $enableSmilies = 1;
    public $permissionCanUseBBCodes = 'user.message.canUseBBCodes';
    public $permissionCanUseHtml = 'user.message.canUseHtml';
    public $permissionCanUseSmilies = 'user.message.canUseSmilies';
    public $showSignature = 0;
    public $showSignatureSetting = 0;
    public $preParse = 0;
    
    public function readParameters(){
        I18nHandler::getInstance()->register('sectionData');
        if (MODULE_SMILEY) {
			$this->smileyCategories = SmileyCache::getInstance()->getCategories();
			foreach ($this->smileyCategories as $index => $category) {
				$category->loadSmilies();
				
				// remove empty categories
				if (!count($category) || $category->isDisabled) {
					unset($this->smileyCategories[$index]);
				}
			}
			
			$firstCategory = reset($this->smileyCategories);
			if ($firstCategory) {
				$this->defaultSmilies = SmileyCache::getInstance()->getCategorySmilies($firstCategory->categoryID ?: null);
			}
		}
		
		if ($this->enableBBCodes && $this->allowedBBCodesPermission) {
			BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission($this->allowedBBCodesPermission)));
		}
    }
    
    public function readFormData(){
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('sectionData')) $this->formData['sectionData'] = MessageUtil::stripCrap(StringUtil::trim(I18nHandler::getInstance()->getValue('sectionData')));
        if (isset($_POST['enableSmilies']) && WCF::getSession()->getPermission($this->permissionCanUseSmilies)) $this->enableSmilies = intval($_POST['enableSmilies']);
        if (isset($_POST['enableHtml']) && WCF::getSession()->getPermission($this->permissionCanUseHtml)) $this->enableHtml = intval($_POST['enableHtml']);
        if (isset($_POST['enableBBCodes']) && WCF::getSession()->getPermission($this->permissionCanUseBBCodes)) $this->enableBBCodes = intval($_POST['enableBBCodes']);
    }
    
    public function validateFormData(){
        if (!I18nHandler::getInstance()->validateValue('sectionData')) {
			if (I18nHandler::getInstance()->isPlainValue('sectionData')) {
				throw new UserInputException('sectionData');
			}
			else {
				throw new UserInputException('sectionData', 'multilingual');
			}
		}
        if ($this->enableBBCodes && $this->allowedBBCodesPermission) {
			$disallowedBBCodes = BBCodeParser::getInstance()->validateBBCodes($this->formData['sectionData'], ArrayUtil::trim(explode(',', WCF::getSession()->getPermission($this->allowedBBCodesPermission))));
			if (!empty($disallowedBBCodes)) {
				WCF::getTPL()->assign('disallowedBBCodes', $disallowedBBCodes);
				throw new UserInputException('sectionData', 'disallowedBBCodes');
			}
		}
    }
    
    public function assignFormVariables(){
        I18nHandler::getInstance()->assignVariables();
        WCF::getTPL()->assign(array('attachmentHandler' => $this->attachmentHandler,
                                    'defaultSmilies' => $this->defaultSmilies,
			                        'enableBBCodes' => $this->enableBBCodes,
			                        'enableHtml' => $this->enableHtml,
			                        'enableSmilies' => $this->enableSmilies,
                                    'permissionCanUseBBCodes' => $this->permissionCanUseBBCodes,
			                        'permissionCanUseHtml' => $this->permissionCanUseHtml,
			                        'permissionCanUseSmilies' => $this->permissionCanUseSmilies,
                                    'showSignature' => $this->showSignature,
			                        'showSignatureSetting' => $this->showSignatureSetting,
			                        'smileyCategories' => $this->smileyCategories,
                                    'preParse' => $this->preParse));
    }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
    
    public function saved($returnValues){
        $additionalData = array();
        $additionalData['enableSmilies'] = $this->enableSmilies;
        $additionalData['enableBBCodes'] = $this->enableBBCodes;
        $additionalData['enableHtml'] = $this->enableHtml;
        $data = array();
        $data['additionalData'] = serialize($additionalData);
        if(I18nHandler::getInstance()->isPlainValue('sectionData')) {
            $data['sectionData'] = $this->formData['sectionData'];
            $editor = new ContentSectionEditor($returnValues['returnValues']);
            $editor->update($data);
        }
        
        $sectionID = $returnValues['returnValues']->sectionID;
            $update = array();
            if (!I18nHandler::getInstance()->isPlainValue('sectionData')) {
                I18nHandler::getInstance()->save('sectionData', 'cms.content.section.'.$sectionID.'.sectionData', 'cms.content.section', PACKAGE_ID);
                $update['sectionData'] = 'cms.content.section.'.$sectionID.'.sectionData';
            }
            if (!empty($update)) {
                $editor = new ContentSectionEditor($returnValues['returnValues']);
                $editor->update($update);
            
            }
        I18nHandler::getInstance()->reset();
    }
    
    public function getOutput($sectionID){
        $section = new ContentSection($sectionID);
        return $this->getFormattedMessage($section);
    }
    
    public function getFormattedMessage($section) {
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($section->sectionData, $additionalData['enableSmilies'], $additionalData['enableHtml'], $additionalData['enableBBCodes']);
	}
}