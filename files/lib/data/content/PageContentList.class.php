<?php
namespace cms\data\content;


class PageContentList extends ViewableContentList{
    
    public $pageID;
    
    public function __construct($pageID){
        $this->pageID = $pageID;        
        parent::__construct();
        
        $this->getConditionBuilder()->add('content.pageID = ?', array($this->pageID));
        
    }
}