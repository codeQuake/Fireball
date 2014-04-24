<?php
namespace cms\data\news;

use wcf\data\DatabaseObjectEditor;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Functions to edit a news.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsEditor extends DatabaseObjectEditor {
	protected static $baseClass = 'cms\data\news\News';

	public function updateCategoryIDs(array $categoryIDs = array()) {
		// remove old assigns
		$sql = "DELETE FROM	cms" . WCF_N . "_news_to_category
			WHERE		newsID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->newsID
		));
		
		// assign new categories
		if (! empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();
			
			$sql = "INSERT INTO	cms" . WCF_N . "_news_to_category
						(categoryID, newsID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute(array(
					$categoryID,
					$this->newsID
				));
			}
			
			WCF::getDB()->commitTransaction();
		}
	}
}
