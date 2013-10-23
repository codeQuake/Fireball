<?php
namespace cms\data\news;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

class NewsList extends DatabaseObjectList{

    public $className = 'cms\data\news\News';
    public $categoryList = true;
    
    public function readObjects() {
		parent::readObjects();
		
		if ($this->categoryList) {
			if (!empty($this->objectIDs)) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('newsID IN (?)', array($this->objectIDs));
				$sql = "SELECT	*
					FROM	cms".WCF_N."_news_to_category
					".$conditionBuilder;
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				while ($row = $statement->fetchArray()) {
					if (isset($this->objects[$row['newsID']])) $this->objects[$row['newsID']]->setCategoryID($row['categoryID']);
				}
			}
		}
	}
    
    public function isCategoryList($enable = true) {
		$this->categoryList = $enable;
	}
}