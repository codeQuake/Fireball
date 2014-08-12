<?php
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\WCF;

//get Pages
$list = new PageList();
$list->readObjects();

foreach ($list->getObjects() as $page) {
	if ($page->layoutID != 0) {

		//get layout
		$sql = "SELECT * FROM cms".WCF_N."_layout WHERE layoutID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($page->layoutID));
		$row = $statement->fetchArray();

		//import data to page
		$update = array('stylesheets' => $row['data']);
		$editor = new PageEditor($page);
		$editor->update($update);

		//kill layout file
		@unlink(CMS_DIR . 'style/layout-' . $page->layoutID . '.css');
	}
}

//clear cache
PageCacheBuilder::getInstance()->reset();

//drop layout column
$sql = "DROP TABLE IF EXISTS cms".WCF_N."_layout";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

//remove from log
$sql = "DELETE FROM wcf".WCF_N."_package_installation_sql_log WHERE sqlTable = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(
	'cms'.WCF_N.'_layout'
));
