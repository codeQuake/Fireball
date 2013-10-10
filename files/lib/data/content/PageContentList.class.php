<?php
namespace cms\data\content;


class PageContentList extends ViewableContentList{
    
    public $pageID = 0;
    public $sqlOrderBy = 'content.showOrder ASC';
    
    public function __construct($pageID){
        $this->pageID = $pageID;        
        parent::__construct();
        
        $this->getConditionBuilder()->add('content.pageID = ?', array($pageID));
        
    }
}