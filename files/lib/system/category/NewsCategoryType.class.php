<?php
namespace cms\system\category;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

class NewsCategoryType extends AbstractCategoryType{

    protected $langVarPrefix = 'cms.category.news';
    
    protected $forceDescription = false;
    
    protected $maximumNestingLevel = 1;
    
    protected $objectTypes = array('com.woltlab.wcf.acl' => 'de.codequake.cms.category.news');
    
    public function getApplication() {
        return 'cms';
    }
    
    public function canAddCategory() {
        return $this->canEditCategory();
    }
    
    public function canDeleteCategory() {
        return $this->canEditCategory();
    }
    
    public function canEditCategory() {
        return WCF::getSession()->getPermission('admin.cms.news.canManageCategory');
    }
}