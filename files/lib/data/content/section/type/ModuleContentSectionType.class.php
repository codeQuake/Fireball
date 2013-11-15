<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use wcf\system\WCF;
use cms\data\module\ModuleList;
use cms\data\module\Module;
use wcf\system\template\TemplateCompiler;
use wcf\system\template\TemplateEngine;

class ModuleContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.module';
    public $moduleList = array();
    public $additionalData = array();
    
    public function readParameters(){
        $list = new ModuleList();
        $list->readObjects();
        $this->moduleList = $list->getObjects();
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $this->formData['sectionData'] = $section->sectionData;
    }
    
    public function readFormData(){
        if(isset($_POST['sectionData'])) $this->formData['sectionData'] = intval($_POST['sectionData']);
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('moduleList' => $this->moduleList,
                                    'moduleID' => isset($this->formData['sectionData']) ? $this->formData['sectionData']:0));
    }
    
    public function getFormTemplate(){
        return 'moduleSectionType';
    }
    
    public function saved($section){
        $data['sectionData'] = $this->formData['sectionData'];
        $editor = new ContentSectionEditor($section);
        $editor->update($data);
        if ($this->action == 'add'){
            $this->formData = array();
        }
    }
    
    public function getOutput($sectionID){
        $section = new ContentSection($sectionID);
        $module = new Module(intval($section->sectionData));
        if($module->php !== null) require(CMS_DIR.'files/php/'.$module->php);
        if($module->tpl !== null) return WCF::getTPL()->fetch($module->tpl, 'cms');
        return '';
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $module = new Module(intval($section->sectionData));
        return '### Module - '.$module->getTitle().'###';
    }
}