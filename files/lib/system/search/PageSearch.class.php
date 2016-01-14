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
	public $cache = array();

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::cacheObjects()
	 */
	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$pageList = new SearchResultPageList();
		$pageList->getConditionBuilder()->add('page.pageID IN (?)', array($objectIDs));
		$pageList->readObjects();

		foreach ($pageList->getObjects() as $page) {
			$this->cache[$page->pageID] = $page;
		}
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getIDFieldName()
	 */
	public function getIDFieldName() {
		return $this->getTableName().'.pageID';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getObject()
	 */
	public function getObject($objectID) {
		if (isset($this->cache[$objectID])) {
			return $this->cache[$objectID];
		}

		return null;
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getSubjectFieldName()
	 */
	public function getSubjectFieldName() {
		return $this->getTableName().'.title';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getTableName()
	 */
	public function getTableName() {
		return 'cms'.WCF_N.'_page';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getTimeFieldName()
	 */
	public function getTimeFieldName() {
		return $this->getTableName().'.creationTime';
	}

	/**
	 * @see	\wcf\system\search\ISearchableObjectType::getUsernameFieldName()
	 */
	public function getUsernameFieldName() {
		return $this->getTableName().'.authorName';
	}
}
