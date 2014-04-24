<?php
namespace cms\system\search;

use cms\data\category\NewsCategory;
use cms\data\news\SearchResultNewsList;
use wcf\form\IForm;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\search\AbstractSearchableObjectType;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsSearch extends AbstractSearchableObjectType {
	public $messageCache = array();

	public function cacheObjects(array $objectIDs, array $additionalData = null) {
		$list = new SearchResultNewsList();
		$list->getConditionBuilder()->add('news.newsID IN (?)', array(
			$objectIDs
		));
		$list->readObjects();
		foreach ($list->getObjects() as $item) {
			$this->messageCache[$item->newsID] = $item;
		}
	}

	public function getApplication() {
		return 'cms';
	}

	public function getObject($objectID) {
		if (isset($this->messageCache[$objectID])) return $this->messageCache[$objectID];
		return null;
	}

	public function getTableName() {
		return 'cms' . WCF_N . '_news';
	}

	public function getIDFieldName() {
		return $this->getTableName() . '.newsID';
	}

	public function getConditions(IForm $form = null) {
		$conditionBuilder = new PreparedStatementConditionBuilder();
		
		// accessible category ids
		$categoryIDs = NewsCategory::getAccessibleCategoryIDs();
		if (empty($categoryIDs)) {
			throw new PermissionDeniedException();
		}
		$conditionBuilder->add($this->getTableName() . '.newsID IN (SELECT newsID FROM cms' . WCF_N . '_news_to_category WHERE categoryID IN (?))', array(
			$categoryIDs
		));
		
		// default conditions
		$conditionBuilder->add($this->getTableName() . '.isDisabled = 0');
		$conditionBuilder->add($this->getTableName() . '.isDeleted = 0');
		
		// language
		if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
			$conditionBuilder->add('(' . $this->getTableName() . '.languageID IN (?) OR ' . $this->getTableName() . '.languageID IS NULL)', array(
				WCF::getUser()->getLanguageIDs()
			));
		}
		
		return $conditionBuilder;
	}
}
