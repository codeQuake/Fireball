<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use wcf\util\StringUtil;

class TextContentSectionType implements IContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.text';
    
    public function readFormData(){
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('sectionData')) $this->formData['sectionData'] = StringUtil::trim(I18nHandler::getInstance()->getValue('sectionData'));
    }
    
    public function validateFormData(){ }
    
    public function saveFormData(){ }
    
    public function assignFormVariables(){ }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
}