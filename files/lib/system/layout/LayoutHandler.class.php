<?php
namespace cms\system\layout;
use wcf\system\SingletonFactory;
use cms\data\layout\LayoutList;


class LayoutHandler extends SingletonFactory{

    public $layoutIDs = array();
    
    public function init(){
        $list = new LayoutList();
        $list->readObjects();
        $list = $list->getObjects();
        
        foreach($list as $item){
            $this->layoutIDs[] = $item->layoutID;
        }
    }
    
    public function getStylesheet($layoutID){
        $filename = CMS_DIR.'style/layout-'.$layoutID.'.css';
        if (!file_exists(WCF_DIR.$filename)) {
            LayoutCompiler::getInstance()->compile(new Layout($layoutID));
        }
        return '<link rel="stylesheet" type="text/css" href="'.$filename.'" />';
    }
}