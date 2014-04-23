<?php
namespace cms\data\category;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the news category cache.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryCache extends SingletonFactory {
	protected $unreadNews = array();
	public $sql;

	protected function initUnreadNews() {
		$this->unreadNews = array();
		
		if (WCF::getUser()->userID) {
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add("news.lastChangeTime > ?", array(
				VisitTracker::getInstance()->getVisitTime('de.codequake.cms.news')
			));
			$conditionBuilder->add("news.isDeleted = 0 AND news.isDisabled = 0");
			$conditionBuilder->add("tracked_visit.visitTime IS NULL");
			// apply language filter
			if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
				$conditionBuilder->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(
					WCF::getUser()->getLanguageIDs()
				));
			}
			
			$sql = "SELECT		COUNT(*) AS count, news_to_category.categoryID
				FROM		cms" . WCF_N . "_news news
				LEFT JOIN	wcf" . WCF_N . "_tracked_visit tracked_visit
				ON		(tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('de.codequake.cms.news') . " AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = " . WCF::getUser()->userID . ")
                LEFT JOIN	cms" . WCF_N . "_news_to_category news_to_category
				ON		(news_to_category.newsID = news.newsID)
				" . $conditionBuilder . "
				GROUP BY	news_to_category.categoryID";
			$this->sql = $sql;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				$this->unreadNews[$row['categoryID']] = $row['count'];
			}
		}
	}

	public function getUnreadNews($categoryID) {
		$this->initUnreadNews();
		if (isset($this->unreadNews[$categoryID])) return $this->unreadNews[$categoryID];
		return 0;
	}
}
