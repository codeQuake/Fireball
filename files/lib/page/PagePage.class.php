<?php
namespace cms\page;
use cms\data\page\Page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\system\comment\CommentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\menu\page\PageMenu;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\exception\PermissionDeniedException;

class PagePage extends AbstractPage{

    public $contentList = array();
    public $page = null;
    public $enableTracking = true;
    
    public $commentObjectTypeID = 0;
    public $commentManager = null;
    public $commentList = null;
    
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
        if(!$this->page->isVisible() || !$this->page->isAccessible()) throw new PermissionDeniedException();
        if (PageMenu::getInstance()->getLandingPage()->menuItem == $this->page->title) {
			WCF::getBreadcrumbs()->remove(0);
		}
               
        
        //breadcrumbs
        foreach($this->page->getParentPages() as $page){
            WCF::getBreadcrumbs()->add(new Breadcrumb($page->getTitle(), 
                                                            LinkHandler::getInstance()->getLink('Page', array('application' => 'cms',
                                                                                                                'object' => $page))));
        }
        
        //get Contents
        $this->contentList = $this->page->getContentList();
        
        //comments
        if($this->page->isCommentable){
            $this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.codequake.cms.page.comment');
            $this->commentManager = CommentHandler::getInstance()->getObjectType($this->commentObjectTypeID)->getProcessor();
            $this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->commentObjectTypeID, $this->page->pageID);
        }
        
        //meta tags
        if($this->page->metaKeywords !== '') MetaTagHandler::getInstance()->addTag('keywords', 'keywords', $this->page->metaKeywords);
        if($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('description', 'description', $this->page->metaDescription);
        if($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('og:description', 'og:description', $this->page->metaDescription, true);
        MetaTagHandler::getInstance()->addTag('robots', 'robots', $this->page->robots);
        MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->page->getTitle() . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
        MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('Page', array('application' => 'cms', 'object' => $this->page)), true);
        MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'article', true);
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('contentList' => $this->contentList,
                                    'page' => $this->page,
                                    'likeData' => ((MODULE_LIKE && $this->commentList) ? $this->commentList->getLikeData() : array()),
                                    'commentCanAdd' => (WCF::getUser()->userID && WCF::getSession()->getPermission('user.cms.page.canAddComment')),
                                    'commentList' => $this->commentList,
                                    'commentObjectTypeID' => $this->commentObjectTypeID,
                                    'lastCommentTime' => ($this->commentList ? $this->commentList->getMinCommentTime() : 0),
                                    'allowSpidersToIndexThisPage' => true));
                                    
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
   
    public function getObjectType(){
        return 'de.codequake.cms.page';
    }
    
    public function getObjectID() {
        return $this->page->pageID;
    }
}