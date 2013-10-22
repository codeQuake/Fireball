<?php
namespace cms\data\news;
use wcf\data\DatabaseObjectEditor;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;


class NewsEditor extends DatabaseObjectEditor{

    protected static $baseClass = 'cms\data\news\News';
    
    public function updateCategoryIDs(array $categoryIDs = array()) {
		// remove old assigns
		$sql = "DELETE FROM	cms".WCF_N."_news_to_category
			WHERE		newsID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->newsID));
		
		// assign new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();
			
			$sql = "INSERT INTO	cms".WCF_N."_news_to_category
						(categoryID, newsID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute(array(
						$categoryID,
						$this->news
				));
			}
			
			WCF::getDB()->commitTransaction();
		}
	}
	
}
