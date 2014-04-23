<?php
namespace cms\system\menu\page;

use cms\data\category\NewsCategory;
use wcf\system\category\CategoryHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\menu\page\DefaultPageMenuItemProvider;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsListPageMenuItemProvider extends DefaultPageMenuItemProvider {
	protected $notifications = null;

	public function getNotifications() {
		if ($this->notifications === null) {
			$this->notifications = 0;
			
			if (WCF::getUser()->userID) {
				// load storage data
				UserStorageHandler::getInstance()->loadStorage(array(
					WCF::getUser()->userID
				));
				
				// get ids
				$data = UserStorageHandler::getInstance()->getStorage(array(
					WCF::getUser()->userID
				), 'cmsUnreadNews');
				
				// cache does not exist or is outdated
				if ($data[WCF::getUser()->userID] === null) {
					$categoryIDs = NewsCategory::getAccessibleCategoryIDs();
					// removed ignored boards
					foreach ($categoryIDs as $key => $categoryID) {
						$category = CategoryHandler::getInstance()->getCategory($categoryID);
					}
					
					if (! empty($categoryIDs)) {
						$conditionBuilder = new PreparedStatementConditionBuilder();
						$conditionBuilder->add("news.lastChangeTime > ?", array(
							VisitTracker::getInstance()->getVisitTime('de.codequake.cms.news')
						));
						$conditionBuilder->add("news.newsID IN (SELECT newsID FROM cms" . WCF_N . "_news_to_category WHERE categoryID IN (?))", array(
							$categoryIDs
						));
						$conditionBuilder->add("news.isDeleted = 0 AND news.isDisabled = 0");
						$conditionBuilder->add("tracked_visit.visitTime IS NULL");
						// apply language filter
						if (LanguageFactory::getInstance()->multilingualismEnabled() && count(WCF::getUser()->getLanguageIDs())) {
							$conditionBuilder->add('(news.languageID IN (?) OR news.languageID IS NULL)', array(
								WCF::getUser()->getLanguageIDs()
							));
						}
						
						$sql = "SELECT		COUNT(*) AS count
							FROM		cms" . WCF_N . "_news news
							LEFT JOIN	wcf" . WCF_N . "_tracked_visit tracked_visit
							ON		(tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('de.codequake.cms.news') . " AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = " . WCF::getUser()->userID . ")
							" . $conditionBuilder;
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute($conditionBuilder->getParameters());
						$row = $statement->fetchArray();
						$this->notifications = $row['count'];
					}
					
					// update storage data
					UserStorageHandler::getInstance()->update(WCF::getUser()->userID, 'cmsUnreadNews', $this->notifications);
				}
				else {
					$this->notifications = $data[WCF::getUser()->userID];
				}
			}
		}
		
		return $this->notifications;
	}
}
