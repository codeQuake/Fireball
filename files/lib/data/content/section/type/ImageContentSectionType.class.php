<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use wcf\system\WCF;
use wcf\util\StringUtil;
use cms\data\file\FileList;
use cms\data\file\File;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class ImageContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.image';
    public $isMultilingual = true;
    public $fileList = array();
    public $additionalData = array();
    
    public function readParameters(){
        $list = new FileList();
        $list->getConditionBuilder()->add('file.type LIKE ?', array('image/%'));
        $list->readObjects();
        $this->fileList = $list->getObjects();
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $this->formData['sectionData'] = $section->sectionData;
        $this->additionalData = @unserialize($section->additionalData);
    }
    
    public function readFormData(){
        if(isset($_POST['sectionData'])) $this->formData['sectionData'] = intval($_POST['sectionData']);        
        if(isset($_POST['subtitle'])) $this->additionalData['subtitle'] = StringUtil::trim($_POST['subtitle']);
        if(isset($_POST['link']))   $this->additionalData['link'] = StringUtil::trim($_POST['link']);
        if(isset($_POST['resizable']))   $this->additionalData['resizable'] = intval($_POST['resizable']);
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('fileList' => $this->fileList,
                                    'fileID' => isset($this->formData['sectionData']) ? $this->formData['sectionData']:0,
                                    'link' => isset($this->additionalData['link']) ? $this->additionalData['link'] : '',
                                    'resizable' => isset($this->additionalData['resizable']) ? $this->additionalData['resizable'] : 0,
                                    'subtitle' => isset($this->additionalData['subtitle']) ? $this->additionalData['subtitle'] : ''));
    }
    
    public function getFormTemplate(){
        return 'imageSectionType';
    }
    
    public function saved($section){
        $data['sectionData'] = $this->formData['sectionData'];
        $data['additionalData'] = serialize($this->additionalData);
        $editor = new ContentSectionEditor($section);
        $editor->update($data);
        if ($this->action == 'add'){
            $this->formData = array();
            $this->additionalData = array();
        }
    }
    
    public function getOutput($sectionID){
        $section = new ContentSection($sectionID);
        $file = new File(intval($section->sectionData));
        $additionalData = @unserialize($section->additionalData);
        WCF::getTPL()->assign(array('image'=> $file,
                                    'link' => isset($additionalData['link']) ? $additionalData['link'] : '',
                                    'subtitle' => isset($additionalData['subtitle']) ? $additionalData['subtitle'] : '',
                                    'resizable' => isset($additionalData['resizable']) ? $additionalData['resizable'] : 0));
        return WCF::getTPL()->fetch('imageSectionTypeOutput', 'cms');
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $file = new File(intval($section->sectionData));
        return '###'.$file->title.'###';
    }
}