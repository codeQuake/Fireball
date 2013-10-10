<?php
namespace cms\data\content\section;

class ContentContentSectionList extends ViewableContentSectionList{
    
    public $contentID = 0;
    public $sqlOrderBy = 'content_section.showOrder ASC';
    
    public function __construct($contentID){
        $this->contentID = $contentID;        
        parent::__construct();
        
        $this->getConditionBuilder()->add('content_section.contentID = ?', array($this->contentID));
        
    }
}