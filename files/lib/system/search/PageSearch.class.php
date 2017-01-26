<?php
namespace cms\system\search;

use cms\data\page\SearchResultPageList;
use wcf\system\search\AbstractSearchableObjectType;

/**
 * ISearchableObjectType implementation for searching pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageSearch extends AbstractSearchableObjectType {
	/**
	 * page cache
	 * @var	array<\cms\data\page\SearchResultPage>
	 */
	public $cache = [];

	/**
	 * @inheritDoc
	 */
	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$pageList = new SearchResultPageList();
		$pageList->getConditionBuilder()->add('page.pageID IN (?)', [$objectIDs]);
		$pageList->readObjects();

		foreach ($pageList->getObjects() as $page) {
			$this->cache[$page->pageID] = $page;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getIDFieldName() {
		return $this->getTableName().'.pageID';
	}

	/**
	 * @inheritDoc
	 */
	public function getObject($objectID) {
		if (isset($this->cache[$objectID])) {
			return $this->cache[$objectID];
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubjectFieldName() {
		return $this->getTableName().'.title';
	}

	/**
	 * @inheritDoc
	 */
	public function getTableName() {
		return 'cms'.WCF_N.'_page';
	}

	/**
	 * @inheritDoc
	 */
	public function getTimeFieldName() {
		return $this->getTableName().'.creationTime';
	}

	/**
	 * @inheritDoc
	 */
	public function getUsernameFieldName() {
		return $this->getTableName().'.authorName';
	}
}
