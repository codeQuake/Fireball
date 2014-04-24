<?php
namespace cms\page;

use cms\data\category\NewsCategory;
use cms\data\news\NewsFeedList;
use wcf\page\AbstractFeedPage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsFeedPage extends AbstractFeedPage {
	public $itemsPerPage = CMS_NEWS_PER_PAGE;

	public function readParameters() {
		parent::readParameters();
		
		if (empty($this->objectIDs)) {
			$this->objectIDs = NewsCategory::getAccessibleCategoryIDs();
		}
		else {
			foreach ($this->objectIDs as $objectID) {
				$category = NewsCategory::getCategory($objectID);
				
				if (! $category->isAccessible()) throw new PermissionDeniedException();
				if ($category === null) throw new IllegalLinkException();
			}
		}
	}

	public function readData() {
		parent::readData();
		$this->title = WCF::getLanguage()->get('cms.page.news');
		
		$this->items = new NewsFeedList($this->objectIDs);
		$this->items->sqlLimit = $this->itemsPerPage;
		$this->items->readObjects();
		
		if (count($this->objectIDs) === 1) {
			$this->title = CategoryHandler::getInstance()->getCategory(reset($this->objectIDs))->getTitle();
		}
	}
}
