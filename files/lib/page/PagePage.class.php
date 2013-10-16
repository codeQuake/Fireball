<?php
namespace cms\page;
use cms\data\page\Page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\menu\page\PageMenu;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;


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
            $this->activeMenuItem = $this->page->title;
        }
    }
    
    public function readData(){
        parent::readData();
        if (PageMenu::getInstance()->getLandingPage()->menuItem == $this->page->title) {
			WCF::getBreadcrumbs()->remove(0);
		}
        
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
                                    
        if($this->page->showSidebar == 1) {
            DashboardHandler::getInstance()->loadBoxes('de.codequake.cms.page', $this);
            WCF::getTPL()->assign(array('sidebarCollapsed'	=> UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.codequake.cms.page'),
                                        'sidebarName' => 'de.codequake.cms.page'));
        }
    }
    
    public function show(){
        if($this->page->hasMenuItem()) $this->activeMenuItem = $this->page->title;
        else{
            //activate startpage-item
            $sql  = "SELECT pageID FROM cms".WCF_N."_page WHERE isHome = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array(1));
            $row = $statement->fetchArray();
            $startPageID = $row['pageID'];
            $startPage = new Page($startPageID);
            $this->activeMenuItem = $startPage->title;
        }
        parent::show();
    }
}