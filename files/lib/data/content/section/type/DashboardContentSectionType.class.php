<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\Content;
use cms\data\content\section\ContentSectionEditor;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\WCF;
use cms\page\PagePage;

class DashboardContentSectionType extends AbstractContentSectionType{
    public $objectType = 'de.codequake.cms.section.type.dashboard';
    public $boxList = null;
    public $content = null;
    
    public function readParameters(){
        $this->boxList = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
        if($this->action == 'add') $this->content = new Content(intval($_REQUEST['id']));
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $this->content = new Content($section->contentID);
        $this->formData['sectionData'] = $section->sectionData;
    }
    
    public function readFormData(){
        if(isset($_POST['sectionData'])) $this->formData['sectionData'] = intval($_POST['sectionData']);
    }
    
    public function assignFormVariables(){
        WCF::getTPL()->assign(array('boxList' => $this->boxList,
                                    'boxID' => isset($this->formData['sectionData']) ? $this->formData['sectionData']:0,
                                    'content' => $this->content));
    }
    
    public function getFormTemplate(){
        return 'dashboardSectionType';
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
        $boxID = (intval($section->sectionData));
        $this->boxList = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
        $className = $this->boxList[$boxID]->className;
        $box = new $className();
        $box->init($this->boxList[$boxID], new PagePage());
        return $box->getTemplate();
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $file = new File(intval($section->sectionData));
        return '###'.$file->title.'###';
    }

}