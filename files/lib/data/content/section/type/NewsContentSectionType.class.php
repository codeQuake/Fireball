<?php
namespace cms\data\content\section\type;
use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionEditor;
use cms\data\news\CategoryNewsList;
use wcf\data\category\Category;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
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
        if (isset($_REQUEST['type'])) $this->additionalData['type'] = intval($_REQUEST['type']);
        else $this->additionalData['type'] = 0;
        if (isset($_REQUEST['limit'])) $this->additionalData['limit'] = intval($_REQUEST['limit']);
        else $this->additionalData['limit'] = CMS_NEWS_LATEST_LIMIT;
    }
    
    
    public function assignFormVariables(){
        
        WCF::getTPL()->assign(array('categoryList' => $this->categoryList,
                                    'categoryIDs' => isset($this->formData['sectionData']) ? $this->formData['sectionData']: array(),
                                    'type' => isset($this->additionalData['type']) ? $this->additionalData['type'] : 0,
                                    'limit' => isset($this->additionalData['limit']) ? $this->additionalData['limit'] : CMS_NEWS_LATEST_LIMIT));
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
            
            $data = @unserialize($section->additionalData);
            $list = new CategoryNewsList($categoryIDs);
            $list->sqlLimit = isset($data['limit']) ? intval($data['limit']) : CMS_NEWS_LATEST_LIMIT;
            $list->readObjects();
            $list = $list->getObjects();
            $type = isset($data['type']) ? intval($data['type']) : 0;
            WCF::getTPL()->assign(array('newsList' => $list, 'type' => $type));
            return WCF::getTPL()->fetch('newsSectionTypeOutput', 'cms');
        }
        return '';
    }
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        $data = @unserialize($section->additionalData);
        $categoryIDs = @unserialize($section->sectionData);
        $categories = array();
        foreach($categoryIDs as $categoryID){
            $category = new Category($categoryID);
            $category = new NewsCategory($category);
            $categories[] = $category->getTitle();
        }
        $type = isset($data['type'])? $data['type'] : 0;
        $limit = isset($data['limit'])? $data['limit'] : 0;
        return StringUtil::truncate('### News: Type: '.$type.'; Limit: '.$limit.'; Categories: '.implode(', ', $categories).'###', 150, "\xE2\x80\xA6", true);;
    }
}
