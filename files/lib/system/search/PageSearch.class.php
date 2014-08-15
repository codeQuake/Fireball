<?php
namespace cms\system\search;

use cms\data\page\SearchResultPageList;
use wcf\system\search\AbstractSearchableObjectType;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PageSearch extends AbstractSearchableObjectType {

	public $cache = array();

	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$pageList = new SearchResultPageList();
		$pageList->getConditionBuilder()->add('page.pageID IN (?)', array($objectIDs));
		$pageList->readObjects();

		foreach ($pageList->getObjects() as $page) {
			$this->cache[$page->pageID] = $page;
		}
	}

	public function getApplication() {
		return 'cms';
	}

	public function getObject($objectID) {
		if (isset($this->cache[$objectID])) return $this->cache[$objectID];
		return null;
	}

	public function getTableName() {
		return 'cms' . WCF_N . '_page';
	}

	public function getIDFieldName() {
		return $this->getTableName() . '.pageID';
	}

	public function getSubjectFieldName() {
		return $this->getTableName() . '.title';
	}

	public function getTimeFieldName() {
		return $this->getTableName() . '.creationTime';
	}

	public function getUsernameFieldName() {
		return $this->getTableName() . '.authorName';
	}
}
