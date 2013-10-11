<?php
namespace cms\page;
use cms\data\page\Page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;

class PagePage extends AbstractPage{

    public $contentList = array();
    public $page = null;
    
    public function readParameters(){
        parent::readParameters();
        if(isset($_REQUEST['id'])) $pageID = intval($_REQUEST['id']);
        if(!isset($pageID)) throw new IllegalLinkException();
        $this->page = new Page($pageID);
        if($this->page->pageID == 0) throw new IllegalLinkException();
    }
    
    public function readData(){
        parent::readData();
        $this->contentList = $this->page->getContentList();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        WCF::getTPL()->assign(array('contentList' => $this->contentList,
                                    'page' => $this->page));
    }
}