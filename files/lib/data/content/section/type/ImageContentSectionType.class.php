<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use cms\data\file\File;
use cms\data\file\FileList;
use cms\data\folder\Folder;
use cms\data\folder\FolderList;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class ImageContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.image';
    public $isMultilingual = true;
    public $fileList = array();
    public $folderList = array();
    public $additionalData = array();
    
    public function readParameters(){
        $list = new FileList();
        $list->getConditionBuilder()->add('file.type LIKE ?', array('image/%'));
        $list->getConditionBuilder()->add('folderID = ?', array(0));
        $list->readObjects();
        $this->fileList = $list->getObjects();
        
        $list = new FolderList();
        $list->readObjects();
        $this->folderList = $list->getObjects();
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        
        //sections from old versions
        $this->formData['sectionData'] = $section->sectionData;
        if(!is_array($this->formData['sectionData'])) $this->formData['sectionData'] = array($this->formData['sectionData']);
        
        //new version
        if(@unserialize($section->sectionData)) $this->formData['sectionData'] = @unserialize($section->sectionData);
        $this->additionalData = @unserialize($section->additionalData);
    }
    
    public function readFormData(){
        if(isset($_POST['sectionData']) && is_array($_POST['sectionData'])) $this->formData['sectionData'] = ArrayUtil::toIntegerArray($_POST['sectionData']);        
        if(isset($_POST['resizable']))   $this->additionalData['resizable'] = intval($_POST['resizable']);
        else $this->additionalData['resizable'] = 0;
        if(isset($_POST['subtitle'])) $this->additionalData['subtitle'] = StringUtil::trim($_POST['subtitle']);
        if(isset($_POST['link']))   $this->additionalData['link'] = StringUtil::trim($_POST['link']);
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('fileList' => $this->fileList,
                                    'folderList' => $this->folderList,
                                    'fileIDs' => isset($this->formData['sectionData']) ? $this->formData['sectionData'] : array(),
                                    'link' => isset($this->additionalData['link']) ? $this->additionalData['link'] : '',
                                    'resizable' => isset($this->additionalData['resizable']) ? $this->additionalData['resizable'] : 0,
                                    'subtitle' => isset($this->additionalData['subtitle']) ? $this->additionalData['subtitle'] : ''));
    }
    
    public function getFormTemplate(){
        return 'imageSectionType';
    }
    
    public function saved($section){
        $data['sectionData'] = serialize($this->formData['sectionData']);
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
        $fileList = array();
        
        //sections from old versions
        $imageIDs = $section->sectionData;
        if(!is_array($imageIDs)) $imageIDs = array($imageIDs);
        
        //new version
        if(@unserialize($section->sectionData)) $imageIDs = @unserialize($section->sectionData);
        $i = 0;
        foreach($imageIDs as $id){
            $file = new File(intval($id));
            $fileList[$i]['file'] = $file;
            $fileList[$i]['folder'] = new Folder($file->folderID);
            $i++;
        }
        $additionalData = @unserialize($section->additionalData);
        
        WCF::getTPL()->assign(array('images'=> $fileList,
                                    'resizable' => isset($additionalData['resizable']) ? $additionalData['resizable'] : 0,
                                    'link' => isset($additionalData['link']) ? $additionalData['link'] : '',
                                    'subtitle' => isset($additionalData['subtitle']) ? $additionalData['subtitle'] : ''
                                    ));
        return WCF::getTPL()->fetch('imageSectionTypeOutput', 'cms');
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $imageIDs = @unserialize($section->sectionData);
        //old version fix
        if(!is_array($imageIDs)) $imageIDs = array($imageIDs);
        $images = array();
        foreach($imageIDs as $imageID){
            $file = new File(intval($imageID));
            $images[] = $file->getTitle();
        }
        
        return StringUtil::truncate('### Images '.implode(', ', $images).'###', 150, "\xE2\x80\xA6", true);
    }
}