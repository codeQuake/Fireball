<?php
namespace cms\page;

use cms\data\content\Content;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\system\counter\VisitCountHandler;
use wcf\data\menu\MenuCache;
use wcf\data\page\PageCache as WCFPageCache;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\page\PageLocationManager;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;

/**
 * Shows a created page.
 *
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractCMSPage extends AbstractPage implements ICMSPage {
	const AVAILABLE_DURING_OFFLINE_MODE = true;

	/**
	 * @inheritDoc
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
	 * @var	\RecursiveIteratorIterator[]
	 */
	public $contentNodeTrees = null;
	
	/**
	 * list of contents
	 * keys body, sidebar are arrays
	 * @var array
	 */
	public $contents = [];
	
	/**
	 * inline editor activated
	 * @var boolean
	 */
	public $editOnInit = false;

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['alias'])) {
			$alias = $_REQUEST['alias'];
			$this->pageID = PageCache::getInstance()->getIDByAlias($alias);
		} else if (isset($_REQUEST['id'])) {
			$this->pageID = intval($_REQUEST['id']);
		} else {
			$home = PageCache::getInstance()->getHomePage();
			if ($home === null) {
				throw new IllegalLinkException();
			}
			$this->pageID = $home->pageID;
		}

		$this->page = PageCache::getInstance()->getPage($this->pageID);
		if ($this->page === null) {
			throw new IllegalLinkException();
		}
		
		// set breadcrumbs manually since wsc's automatic generation returns the root url only
		foreach ($this->page->getParentPages(false) as $parentPage) {
			if ($parentPage->wcfPageID) {
				$page = WCFPageCache::getInstance()->getPage($parentPage->wcfPageID);
				PageLocationManager::getInstance()->addParentLocation($page->identifier, $parentPage->pageID, $parentPage);
			}
		}

		// check if offline and view page or exit
		if (OFFLINE) {
			if (!WCF::getSession()->getPermission('admin.general.canViewPageDuringOfflineMode') && !$this->page->availableDuringOfflineMode) {
				@header('HTTP/1.1 503 Service Unavailable');
				WCF::getTPL()->assign([
					'templateName' => 'offline',
					'templateNameApplication' => 'wcf'
				]);
				WCF::getTPL()->display('offline');
				exit;
			}
		}

		// check permissions
		if (!$this->page->canRead()) {
			throw new PermissionDeniedException();
		}
		
		if (isset($_GET['editOnInit']) && $_GET['editOnInit']) {
			$this->editOnInit = true;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// set active menu item
		$menuItem = PageCache::getInstance()->getActiveMenuItem($this->page->pageID);
		if ($menuItem !== null) {
			$mainMenu = MenuCache::getInstance()->getMainMenu();
			$menuItemNodeList = $mainMenu->getMenuItemNodeList();
			/** @var \wcf\data\menu\item\MenuItemNode $menuItemNode */
			foreach ($menuItemNodeList as $menuItemNode) {
				if ($menuItemNode->itemID == $menuItem->itemID) {
					$menuItemNode->setIsActive();
				}
			}
		}
		
		// get contents
		if ($this->editOnInit) {
			$this->contentNodeTrees = $this->page->getContents();
		} else {
			$contents = $this->page->getContents();
			foreach (Content::$AVAILABLE_POSITIONS as $position) {
				$this->contentNodeTrees[$position] = !empty($contents[$position]) ? $contents[$position] : [];
			}
		}

		// meta tags
		if ($this->page->canRead()) {
			if ($this->page->metaKeywords !== '') MetaTagHandler::getInstance()->addTag('keywords', 'keywords', WCF::getLanguage()->get($this->page->metaKeywords));
			if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('description', 'description', WCF::getLanguage()->get($this->page->metaDescription));
			if ($this->page->metaDescription !== '') MetaTagHandler::getInstance()->addTag('og:description', 'og:description', WCF::getLanguage()->get($this->page->metaDescription), true);
			MetaTagHandler::getInstance()->addTag('generator', 'generator', 'Fireball CMS');
			MetaTagHandler::getInstance()->addTag('og:title', 'og:title', $this->page->getTitle() . ' - ' . WCF::getLanguage()->get(PAGE_TITLE), true);
			MetaTagHandler::getInstance()->addTag('og:url', 'og:url', $this->page->getLink(false), true);
			if (FACEBOOK_PUBLIC_KEY != '') MetaTagHandler::getInstance()->addTag('fb:app_id', 'fb:app_id', FACEBOOK_PUBLIC_KEY, true);
			MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'website', true);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'contentNodeTrees' => $this->contentNodeTrees,
			'availablePositions' => empty($this->contentNodeTrees) ? [] : array_keys($this->contentNodeTrees),
			'page' => $this->page,
			'allowSpidersToIndexThisPage' => $this->page->allowIndexing
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function show() {
		if ($this->editOnInit) {
			$this->templateName = 'pageEditor';
		} else {
			// register visit
			VisitCountHandler::getInstance()->count();
			
			// count click
			$pageEditor = new PageEditor($this->page);
			$pageEditor->updateCounters([
				'clicks' => 1
			]);
		}
		
		parent::show();
	}

	/**
	 * @inheritDoc
	 */
	public function getPage() {
		return $this->page;
	}
}
