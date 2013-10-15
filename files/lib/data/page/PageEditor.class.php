<?php
namespace cms\data\page;
use wcf\system\WCF;
use wcf\data\DatabaseObjectEditor;

class PageEditor extends DatabaseObjectEditor{
    protected static $baseClass = 'cms\data\page\Page';
    
    public function setAsHome() {
		$sql = "UPDATE	cms".WCF_N."_page
			SET	isHome = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(0));
		
		$sql = "UPDATE	cms".WCF_N."_page
			SET	isHome = ?
			WHERE	pageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			1,
			$this->pageID
		));
		
	}
}