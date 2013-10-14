<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use cms\data\content\section\ContentSectionEditor;
use wcf\util\StringUtil;
use cms\data\content\section\ContentSection;
use wcf\system\WCF;

class HeadlineContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.headline';
    public $isMultilingual = true;
    
    public $hlType = '';
    public $additionalData = array();
    
    public function readParameters(){
        I18nHandler::getInstance()->register('sectionData');
    }
    
    public function readFormData(){
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('sectionData')) $this->formData['sectionData'] = StringUtil::trim(I18nHandler::getInstance()->getValue('sectionData'));
        if(isset($_POST['hlType'])) $this->hlType = StringUtil::trim($_POST['hlType']);
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
        WCF::getTPL()->assign(array('hlType' => $this->hlType));
    }
    
    public function getFormTemplate(){
        return 'headlineSectionType';
    }
    
    public function saved($returnValues){
        $additionalData = array();
        $additionalData['hlType'] = $this->hlType;
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
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
        
        return '<'.$additionalData['hlType'].'>'.$section->sectionData.'</'.$additionalData['hlType'].'>';
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
        return WCF::getLanguage()->get('cms.acp.content.section.type.de.codequake.cms.section.type.headline').' '.$additionalData['hlType'].' -> '.$section->sectionData;
    }
}