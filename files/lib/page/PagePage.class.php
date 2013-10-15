<?php
namespace cms\page;
use cms\data\page\Page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\breadcrumb\Breadcrumb;

class PagePage extends AbstractPage{

    public $contentList = array();
    public $page = null;
    
    public function readParameters(){
        parent::readParameters();
        $pageID = 0;
        if(isset($_REQUEST['id'])) $pageID = intval($_REQUEST['id']);
        $this->page = new Page($pageID);
        if($this->page->pageID == 0) {
            $sql  = "SELECT pageID FROM cms".WCF_N."_page WHERE isHome = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array(1));
            $row = $statement->fetchArray();
            $this->pageID = $row['pageID'];
            $this->page = new Page($this->pageID);
        }
    }
    
    public function readData(){
        parent::readData();
        $this->contentList = $this->page->getContentList();
        
        foreach($this->page->getParentPages() as $page){
            WCF::getBreadcrumbs()->add(new Breadcrumb($page->getTitle(), 
                                                            LinkHandler::getInstance()->getLink('Page', array('application' => 'cms',
                                                                                                                'object' => $page))));
        }
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        WCF::getTPL()->assign(array('contentList' => $this->contentList,
                                    'page' => $this->page));
    }
}