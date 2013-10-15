<?php
namespace cms\data\stylesheet;
use cms\data\layout\Layout;

class LayoutStylesheetList extends ViewableStylesheetList{
    
    public $layoutID = 0;
    
    public function __construct($layoutID){
        $this->layoutID = $layoutID;
        $layout = new Layout($this->layoutID);
        $data = @unserialize($layout->data);
        parent::__construct();
        $this->getConditionBuilder()->add('stylesheet.sheetID IN (?)', array($data));
        
    }
}