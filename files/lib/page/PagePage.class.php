<?php
namespace cms\page;

use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\system\counter\VisitCountHandler;
use cms\system\CMSCore;
use wcf\page\AbstractPage;
use wcf\system\comment\CommentHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePage extends AbstractPage {

	const AVAILABLE_DURING_OFFLINE_MODE = true;

	public $contentNodeTree;

	public $sidebarNodeTree;

	public $page = null;

	public $pageID = 0;

	public $enableTracking = true;

	public $commentObjectTypeID = 0;

	public $commentManager = null;

	public $commentList = null;

	public function readParameters() {
		parent::readParameters();
		$alias = '';
		if (isset($_REQUEST['alias'])) $alias = StringUtil::trim($_REQUEST['alias']);
		if ($alias != '') {
			$this->pageID = PageCache::getInstance()->getIDByAlias($alias);
			if ($this->pageID == 0) throw new IllegalLinkException();
			$this->page = PageCache::getInstance()->getPage($this->pageID);
			if ($this->page === null) throw new IllegalLinkException();
		} else {
			$this->page = PageCache::getInstance()->getHomePage($this->pageID);
			if ($this->page->pageID == 0) {
				throw new IllegalLinkException();
			}
		}
		
		//check permission
		if (! $this->page->isVisible() || ! $this->page->isAccessible()) throw new PermissionDeniedException();
		
		// check if offline and view page or exit
		// see: wcf\system\request\RequestHandler
		if (OFFLINE) {
			if (! WCF::getSession()->getPermission('admin.general.canViewPageDuringOfflineMode') && ! $this->page->availableDuringOfflineMode) {
				WCF::getTPL()->assign(array(
					'templateName' => 'offline'
				));
				WCF::getTPL()->display('offline');
				exit();
			}
		}
	}

	public function readData() {
		parent::readData();
		
		//set menuitem
		CMSCore::setActiveMenuItem($this->page);
		
		//set breadcrumbs
		CMSCore::setBreadcrumbs($this->page);
		// get Contents
		$contents = $this->page->getContents();
		$this->contentNodeTree = $contents['body'];
		$this->sidebarNodeTree = $contents['sidebar'];
		
		// comments
		if ($this->page->isCommentable) {
			$this->commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.codequake.cms.page.comment');
			$this->commentManager = CommentHandler::getInstance()->getObjectType($this->commentObjectTypeID)->getProcessor();
			$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->commentObjectTypeID, $this->page->pageID);
		}
		
		// meta tags
		if ($this->page->metaKeywords !== '') MetaTagHandler::getInstance()->addTag('keywords', 'keywords', WCF::getLanguage()->get($this->page->metaKeywords));
		if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('description', 'description', WCF::getLanguage()->get($this->page->metaDescription));
		if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('og:description', 'og:description', WCF::getLanguage()->get($this->page->metaDescription), true);
		MetaTagHandler::getInstance()->addTag('robots', 'robots', $this->page->robots);
		MetaTagHandler::getInstance()->addTag('generator', 'generator', 'Fireball CMS');
		MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->page->getTitle() . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
		MetaTagHandler::getInstance()->addTag('og:url', 'og:url', $this->page->getLink(), true);
		if (FACEBOOK_PUBLIC_KEY != '') MetaTagHandler::getInstance()->addTag('fb:app_id', 'fb:app_id', FACEBOOK_PUBLIC_KEY, true);
		MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'website', true);
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'contentNodeTree' => $this->contentNodeTree,
			'sidebarNodeTree' => $this->sidebarNodeTree,
			'page' => $this->page,
			'likeData' => ((MODULE_LIKE && $this->commentList) ? $this->commentList->getLikeData() : array()),
			'commentCanAdd' => (WCF::getUser()->userID && WCF::getSession()->getPermission('user.cms.page.canAddComment')),
			'commentList' => $this->commentList,
			'commentObjectTypeID' => $this->commentObjectTypeID,
			'lastCommentTime' => ($this->commentList ? $this->commentList->getMinCommentTime() : 0),
			'allowSpidersToIndexThisPage' => true
		));
		
		// sidebar
		if ($this->page->showSidebar == 1) DashboardHandler::getInstance()->loadBoxes('de.codequake.cms.page', $this);
		WCF::getTPL()->assign(array(
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.codequake.cms.page'),
			'sidebarName' => 'de.codequake.cms.page'
		));
	}

	public function show() {
		parent::show();
		// register visit
		VisitCountHandler::getInstance()->count();
		
		// count click
		$pageEditor = new PageEditor($this->page);
		$pageEditor->updateCounters(array(
			'clicks' => 1
		));
	}

	public function getObjectType() {
		return 'de.codequake.cms.page';
	}

	public function getObjectID() {
		if (isset($this->page->pageID)) return $this->page->pageID;
		return 0;
	}
}
