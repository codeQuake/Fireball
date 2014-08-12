<?php
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\WCF;

// add stylesheets column to page table
// added here, because this column must exist for converting
$sql = "ALTER TABLE cms1_page ADD COLUMN stylesheets MEDIUMTEXT AFTER clicks";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

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
