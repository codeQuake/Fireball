<?php
namespace cms\page;

use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\system\counter\VisitCountHandler;
use cms\system\CMSCore;
use wcf\data\menu\item\MenuItem;
use wcf\page\AbstractPage;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\page\PageLocationManager;
use wcf\system\request\LinkHandler;
use wcf\system\style\StyleHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows a created page.
 *
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractCMSPage extends AbstractPage {
	const AVAILABLE_DURING_OFFLINE_MODE = true;

	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;

	/**
	 * page id
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * page object
	 * @var	\cms\data\page\Page
	 */
	public $page = null;

	/**
	 * list of sidebar nodes
	 * @var	\RecursiveIteratorIterator
	 */
	public $sidebarContentNodeTree = null;
	
	/**
	 * list of contents
	 * keys body, sidebar are arrays
	 * @var array
	 */
	public $contents = array();

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// alias for indicating the requested page
		if (isset($_REQUEST['alias'])) {
			$alias = $_REQUEST['alias'];
			$this->pageID = PageCache::getInstance()->getIDByAlias($alias);
		}
		// fallback to id as page indicator. Needed for backward
		// compatibility and due to rare situations where WCF (or 3rd
		// parties) doesn't respect link manipulation for menu items
		else if (isset($_REQUEST['id'])) {
			$this->pageID = intval($_REQUEST['id']);
		}
// 		// no indicator provided
// 		else {
// 			// landing page of the cms
// 			$page = PageCache::getInstance()->getHomePage();
// 			if ($page !== null) {
// 				$this->pageID = $page->pageID;
// 			} else {
// 				// redirect to system's landing page
// 				HeaderUtil::redirect(Linkhandler::getInstance()->getLink(), true);
// 				exit;
// 			}
// 		}

		$this->page = PageCache::getInstance()->getPage($this->pageID);
		if ($this->page === null) {
			throw new IllegalLinkException();
		}

		// check if offline and view page or exit
		// @see	\wcf\system\request\RequestHandler
		if (OFFLINE) {
			if (!WCF::getSession()->getPermission('admin.general.canViewPageDuringOfflineMode') && !$this->page->availableDuringOfflineMode) {
				@header('HTTP/1.1 503 Service Unavailable');
				WCF::getTPL()->assign(array(
					'templateName' => 'offline',
					'templateNameApplication' => 'wcf'
				));
				WCF::getTPL()->display('offline');

				exit;
			}
		}

		// check permissions
		if (!$this->page->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// set active menu item
		$menuItem = new MenuItem($this->page->menuItemID);
		$this->activeMenuItem = $menuItem->identifier;

		// set breadcrumbs
		PageLocationManager::getInstance()->addParentLocation('de.codequake.cms.Page', $this->pageID, $this->page);
		foreach ($this->page->getParentPages() as $parent) {
			PageLocationManager::getInstance()->addParentLocation('de.codequake.cms.Page', $parent->pageID, $parent);
		}

		// get contents
		$this->contents = $this->page->getContents();
		$this->sidebarContentNodeTree = $this->contents['sidebar'];

		// meta tags
		if ($this->page->metaKeywords !== '') MetaTagHandler::getInstance()->addTag('keywords', 'keywords', WCF::getLanguage()->get($this->page->metaKeywords));
		if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('description', 'description', WCF::getLanguage()->get($this->page->metaDescription));
		if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('og:description', 'og:description', WCF::getLanguage()->get($this->page->metaDescription), true);
		MetaTagHandler::getInstance()->addTag('generator', 'generator', 'Fireball CMS');
		MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->page->getTitle() . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
		MetaTagHandler::getInstance()->addTag('og:url', 'og:url', $this->page->getLink(false), true);
		if (FACEBOOK_PUBLIC_KEY != '') MetaTagHandler::getInstance()->addTag('fb:app_id', 'fb:app_id', FACEBOOK_PUBLIC_KEY, true);
		MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'website', true);
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'sidebarContentNodeTree' => $this->sidebarContentNodeTree,
			'page' => $this->page,
			'allowSpidersToIndexThisPage' => $this->page->allowIndexing
		));
	}

	/**
	 * @see	\wcf\page\IPage::show()
	 */
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

	/**
	 * @see	\wcf\page\ITrackablePage::getObjectType()
	 */
	public function getObjectType() {
		return 'de.codequake.cms.page';
	}

	/**
	 * @see	\wcf\page\ITrackablePage::getObjectID()
	 */
	public function getObjectID() {
		return $this->pageID;
	}
}
