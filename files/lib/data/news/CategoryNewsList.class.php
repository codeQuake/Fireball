<?php
namespace cms\data\news;

use cms\data\category\NewsCategory;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CategoryNewsList extends ViewableNewsList {

	public function __construct(array $categoryIDs) {
		parent::__construct();
		if (!empty($categoryIDs)) {
			$this->getConditionBuilder()->add('news_to_category.categoryID IN (?)', array(
				$categoryIDs
			));
			$this->getConditionBuilder()->add('news.newsID = news_to_category.newsID');
		} else $this->getConditionBuilder()->add('1=0');
		foreach ($categoryIDs as $categoryID) {
			$category = new NewsCategory(CategoryHandler::getInstance()->getCategory($categoryID));
			if (! $category->getPermission('canViewDelayedNews')) $this->getConditionBuilder()->add('news.isDisabled = ?', array(
				0
			));
		}
		if (! WCF::getSession()->getPermission('mod.cms.news.canModerateNews')) $this->getConditionBuilder()->add('news.isDisabled = 0');
		if (! WCF::getSession()->getPermission('mod.cms.news.canModerateNews')) $this->getConditionBuilder()->add('news.isDeleted = 0');
	}

	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = "SELECT	news_to_category.newsID AS objectID
				FROM	cms".WCF_N."_news_to_category news_to_category,
						cms".WCF_N."_news news
						".$this->sqlConditionJoins."
						".$this->getConditionBuilder()."
						".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}

	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms".WCF_N."_news_to_category news_to_category,
				cms".WCF_N."_news news
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		return $row['count'];
	}
}
