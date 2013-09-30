<?php
namespace cms\data\content\section;

class ContentContentSectionList extends ViewableContentSectionList{
    
    public $contentID;
    
    public function __construct($contentID){
        $this->contentID = $contentID;        
        parent::__construct();
        
        $this->getConditionBuilder()->add('content_section.contentID = ?', array($this->contentID));
        
    }
}