<?php
namespace cms\system\user\activity\point;

use wcf\data\object\type\ObjectType;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\user\activity\point\IUserActivityPointObjectProcessor;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsUserActivityPointObjectProcessor implements IUserActivityPointObjectProcessor {
	public $objectType = null;
	public $limit = 5000;

	public function __construct(ObjectType $objectType) {
		$this->objectType = $objectType;
	}

	public function countRequests() {
		$sql = "SELECT  COUNT(*) AS count
            FROM    cms" . WCF_N . "_news";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		return ceil($row['count'] / $this->limit) + 1;
	}

	public function updateActivityPointEvents($request) {
		if ($request == 0) {
			// first request
			$sql = "DELETE FROM	wcf" . WCF_N . "_user_activity_point_event 
            WHERE   objectTypeID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->objectType->objectTypeID
			));
		}
		else {
			// others
			
			// get newsIDs
			$sql = "SELECT news.newsID
                FROM    cms" . WCF_N . "_news news
                    AND news.userID IS NOT NULL   
                ORDER BY news.newsID ASC";
			$statement = WCF::getDB()->prepareStatement($sql, $this->limit, ($this->limit * ($request - 1)));
			$statement->execute();
			$newsIDs = array();
			while ($row = $statement->fetchArray()) {
				$newsIDs[] = $row['newsID'];
			}
			
			if (empty($newsIDs)) return;
			
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add("objectTypeID = ?", array(
				$this->objectType->objectTypeID
			));
			$conditionBuilder->add("objectID IN (?)", array(
				$newsIDs
			));
			
			// kill old values
			$sql = "DELETE FROM	wcf" . WCF_N . "_user_activity_point_event 
                " . $conditionBuilder;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			
			// prepare Uranus
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add("newsID IN (?)", array(
				$newsIDs
			));
			// as in ReceivedLikesUserActivtityPointObjectProcessor
			$sql = "INSERT INTO 
                    wcf" . WCF_N . "_user_activity_point_event (userID, objectTypeID, objectID, additionalData)
                    SELECT	userID,
                        ?, 
                        newsID AS objectID,
                        ?
                    FROM	cms" . WCF_N . "_news
                    " . $conditionBuilder;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array_merge((array) $this->objectType->objectTypeID, (array) serialize(array()), $conditionBuilder->getParameters()));
		}
	}
}
