<?php
namespace cms\data\content\section\type;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use cms\data\news\CategoryNewsList;
use cms\data\category\NewsCategoryNodeTree;
use wcf\data\category\Category;
use cms\data\category\NewsCategory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class NewsContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.news';
    public $isMultilingual = true;
    public $additionalData = array();
    public $categoryList = null;
    
    public function readParameters(){
        $categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news');
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $this->formData['sectionData'] = @unserialize($section->sectionData);
        $this->additionalData = @unserialize($section->additionalData);
    }
    
    public function readFormData(){
        if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->formData['sectionData'] = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
        if (isset($_REQUEST['small'])) $this->additionalData['small'] = intval($_REQUEST['small']);
        else $this->additionalData['small'] = 0;
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('categoryList' => $this->categoryList,
                                    'categoryIDs' => isset($this->formData['sectionData']) ? $this->formData['sectionData']: array(),
                                    'small' => isset($this->additionalData['small']) ? $this->additionalData['small'] : 0));
    }
    
    public function getFormTemplate(){
        return 'newsSectionType';
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
        $categoryIDs = @unserialize($section->sectionData);
        foreach($categoryIDs as $categoryID){
            $category = new Category($categoryID);
            $category = new NewsCategory($category);
            if(!$category->getPermission('canViewNews')){
                $index = array_search($categoryID, $categoryIDs);
                if(isset($index)){
                    unset($categoryIDs[$index]);
                    $categoryIDs = array_values($categoryIDs);
                }
            }
        }
        if(!empty($categoryIDs)){
            $list = new CategoryNewsList($categoryIDs);
            $list->sqlLimit = CMS_NEWS_LATEST_LIMIT;
            $list->readObjects();
            $list = $list->getObjects();
            $data = @unserialize($section->additionalData);
            $small = isset($data['small']) ? intval($data['small']) : 0;
            WCF::getTPL()->assign(array('newsList' => $list, 'small' => $small));
            return WCF::getTPL()->fetch('newsSectionTypeOutput', 'cms');
        }
        return '';
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $data = @unserialize($section->additionalData);
        $small = isset($data['small']) && intval($data['small']) == 1 ? 'small' : 'normal';
        return '### News '.$small.' CIDs: '.implode(@unserialize($section->sectionData.'###'),', ').'###';
    }
    
}