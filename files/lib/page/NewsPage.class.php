<?php
namespace cms\page;
use cms\data\news\News;
use cms\data\news\ViewableNews;
use cms\data\news\NewsEditor;
use cms\data\news\NewsAction;
use wcf\page\AbstractPage;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\MetaTagHandler;
use wcf\util\StringUtil;
use wcf\system\WCF;

class NewsPage extends AbstractPage{

    public $activeMenuItem = 'cms.page.news';
    public $enableTracking = true;
    
    public $newsID = 0;
    public $news = null;
    
    public $commentObjectTypeID = 0;
    public $commentManager = null;
    public $commentList = null;
    
    
    public function readParameters(){
        parent::readParameters();
        
        if(isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
        $this->news = ViewableNews::getNews($this->newsID);
        if($this->news->newsID == 0) throw new IllegalLinkException();
    }
    
    public function readData(){
        parent::readData();
        WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), 
                                                            LinkHandler::getInstance()->getLink('NewsCategoryList', array('application' => 'cms'))));
                                                            
        $this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.codequake.cms.news.comment');
        $this->commentManager = CommentHandler::getInstance()->getObjectType($this->commentObjectTypeID)->getProcessor();
        $this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->commentObjectTypeID, $this->newsID);
        
        MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->news->subject . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
		MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('News', array('application' => 'cms', 'object' => $this->news->getDecoratedObject())), true);
		MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'article', true);
		MetaTagHandler::getInstance()->addTag('og:description', 'og:description', StringUtil::decodeHTML(StringUtil::stripHTML($this->news->getExcerpt())), true);
        
        $newsEditor = new NewsEditor($this->news->getDecoratedObject());
        $newsEditor->update(array('clicks' => $this->news->clicks+1));
        
        //if ($this->news->isNew()) {
			$newsAction = new NewsAction(array($this->news->getDecoratedObject()), 'markAsRead', array(
				'viewableNews' => $this->news
			));
			$newsAction->executeAction();
		//}
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        DashboardHandler::getInstance()->loadBoxes('de.codequake.cms.news.news', $this);
        
        WCF::getTPL()->assign(array('newsID' => $this->newsID,
                                    'news' => $this->news,
                                    'commentCanAdd' => (WCF::getUser()->userID && WCF::getSession()->getPermission('user.cms.news.canAddComment')),
                                    'commentList' => $this->commentList,
                                    'commentObjectTypeID' => $this->commentObjectTypeID,
                                    'lastCommentTime' => ($this->commentList ? $this->commentList->getMinCommentTime() : 0),
                                    'allowSpidersToIndexThisPage' => true,
                                    'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.codequake.cms.news.news'),
                                    'sidebarName' => 'de.codequake.cms.news.news'));
    }
}