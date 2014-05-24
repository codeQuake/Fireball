<?php
namespace cms\page;

use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\news\CategoryNewsList;
use wcf\page\SortablePage;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\LinkHandler;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\MetaTagHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsArchivePage extends SortablePage {
	public $activeMenuItem = 'cms.page.news.archive';
	public $enableTracking = true;
	public $neededModules = array(
		'MODULE_NEWS'
	);
	public $itemsPerPage = CMS_NEWS_PER_PAGE;
	public $limit = 10;
	public $categoryList = null;
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';


	protected function initObjectList() {
		$categoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if ($categoryIDs) {
			$this->objectList = new CategoryNewsList($categoryIDs);
		}
		else
			throw new PermissionDeniedException();
	}
}
