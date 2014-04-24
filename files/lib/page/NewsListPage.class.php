<?php
namespace cms\page;

use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\news\CategoryNewsList;
use wcf\page\SortablePage;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\category\CategoryHandler;
use cms\system\counter\VisitCountHandler;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsListPage extends SortablePage {
	public $activeMenuItem = 'cms.page.news';
	public $enableTracking = true;
	public $neededModules = array(
		'MODULE_NEWS'
	);
	public $itemsPerPage = CMS_NEWS_PER_PAGE;
	public $validSortFields = array(
		'username',
		'newsID',
		'time',
		'subject',
		'clicks',
		'comments'
	);
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	public $categoryID = 0;
	public $category = null;
	public $categoryList = null;

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
		$this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);
		if ($this->category === null) {
			throw new IllegalLinkException();
		}
		$this->category = new NewsCategory($this->category);
		if (! $this->category->isAccessible()) {
			throw new PermissionDeniedException();
		}
	}

	protected function initObjectList() {
		if ($this->category) {
			$this->objectList = new CategoryNewsList(array(
				$this->category->categoryID
			));
		}
		else
			throw new IllegalLinkException();
	}

	public function readData() {
		parent::readData();
		VisitCountHandler::getInstance()->count();
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), LinkHandler::getInstance()->getLink('NewsCategoryList', array(
			'application' => 'cms'
		))));
		
		// get categories
		$categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news');
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
	}

	public function assignVariables() {
		parent::assignVariables();
		
		DashboardHandler::getInstance()->loadBoxes('de.codequake.cms.news.newsList', $this);
		WCF::getTPL()->assign(array(
			'category' => $this->category,
			'categoryID' => $this->categoryID,
			'controller' => 'NewsList',
			'allowSpidersToIndexThisPage' => true,
			'sidebarCollapsed' => UserCollapsibleContentHandler::getInstance()->isCollapsed('com.woltlab.wcf.collapsibleSidebar', 'de.codequake.cms.news.newsList'),
			'sidebarName' => 'de.codequake.cms.news.newsList',
			'categoryList' => $this->categoryList
		));
	}

	public function getObjectType() {
		return 'de.codequake.cms.category.news';
	}

	public function getObjectID() {
		return $this->categoryID;
	}
}
