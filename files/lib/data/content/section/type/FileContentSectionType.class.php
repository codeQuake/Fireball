<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use wcf\system\WCF;
use cms\data\file\FileList;
use cms\data\file\File;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class FileContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.file';
    public $isMultilingual = true;
    public $fileList = array();
    public $additionalData = array();
    
    public function readParameters(){
        $list = new FileList();
        $list->readObjects();
        $this->fileList = $list->getObjects();
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $this->formData['sectionData'] = $section->sectionData;
    }
    
    public function readFormData(){
        if(isset($_POST['sectionData'])) $this->formData['sectionData'] = intval($_POST['sectionData']);
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('fileList' => $this->fileList,
                                    'fileID' => isset($this->formData['sectionData']) ? $this->formData['sectionData']:0));
    }
    
    public function getFormTemplate(){
        return 'fileSectionType';
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
        $file = new File(intval($section->sectionData));
        WCF::getTPL()->assign('file', $file);
        return WCF::getTPL()->fetch('fileSectionTypeOutput', 'cms');
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $file = new File(intval($section->sectionData));
        return '###'.$file->title.'###';
    }
}