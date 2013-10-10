<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use cms\data\content\section\ContentSectionEditor;
use wcf\util\StringUtil;

class TextContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.text';
    public $isMultilingual = true;
    
    public function readParameters(){
        I18nHandler::getInstance()->register('sectionData');
    }
    
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
    
    public function assignFormVariables(){
        I18nHandler::getInstance()->assignVariables();
    }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
    
    public function saved($returnValues){
        if(I18nHandler::getInstance()->isPlainValue('sectionData')) {
            $data = array('sectionData' => $this->formData['sectionData']);
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
}