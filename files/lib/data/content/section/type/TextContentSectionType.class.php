<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use wcf\util\StringUtil;

class TextContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.text';
    public $isMultilingual = true;
    
    public function readFormData(){
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('sectionData')) $this->formData['sectionData'] = StringUtil::trim(I18nHandler::getInstance()->getValue('sectionData'));
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
    }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
}