<?php
use cms\data\file\FileEditor;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015-2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
 
//category object type
$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.codequake.cms.file');
 
//get folders
$folders = new FolderList();
$folders->readObjects();
//create categories
$oldIDs = array();
foreach ($folders->getObjects() as $folder) {
	$objectAction = new CategoryAction(array(), 'create', array(
		'data' => array(
				'description' => '',
				'isDisabled' => 0,
				'objectTypeID' => $objectType->objectTypeID,
				'parentCategoryID' => null,
				'showOrder' => null,
				'title' => $folder->getTitle()
			)	
		));
	$objectAction->executeAction();
	$returnValues = $objectAction->getReturnValues();
	$categoryID = $returnValues['returnValues']->categoryID;
	$oldIDs[$folder->folderID] = $categoryID;
}
//add default category
$objectAction = new CategoryAction(array(), 'create', array(
		'data' => array(
				'description' => '',
				'isDisabled' => 0,
				'objectTypeID' => $objectType->objectTypeID,
				'parentCategoryID' => null,
				'showOrder' => null,
				'title' => 'default'
			)	
		));
$objectAction->executeAction();
$returnValues = $objectAction->getReturnValues();
$defaultCategoryID = $returnValues['returnValues']->categoryID;

//get files into categories
$list = new FileList();
$list->readObjects();
foreach ($list->getObjects() as $file) {
	$sql = "INSERT INTO cms".WCF_N."_file_to_category VALUES (?, ?)";
	$statement = WCF::getDB()->prepareStatement($sql);
	//check whether file has folder - if not -> default category
	if ($file->folderID) $categoryID = $oldIDs[$file->folderID];
	else $categoryID = $defaultCategoryID;
	$statement->execute(array($file->fileID, $categoryID));
}

//drop folder ID
$sql = "ALTER TABLE cms".WCF_N."_file DROP folderID;";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

//Drop folder table
$sql = "DROP TABLE IF EXISTS cms".WCF_N."_folder";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

//hash files & copy
copyFiles(CMS_DIR . 'files', CMS_DIR . 'files');

foreach ($list->getObjects() as $file) {
	//old file
	if ($file->fileHash == '') {
		$editor = new FileEditor($file);
		$fileHash = sha1_file(CMS_DIR . 'files/' . $file->filename);
		$folder = substr($fileHash, 0, 2);
		if (!is_dir(CMS_DIR . 'files/' . $folder)) FileUtil::makePath(CMS_DIR . 'files/' . $folder);
		copy (CMS_DIR . 'files/' . $file->filename, CMS_DIR . 'files/' . $folder . '/' . $file->fileID . '-' . $fileHash);
		@unlink(CMS_DIR . 'files/' . $file->filename);
		$editor->update(array('fileHash' => $fileHash));
	}
}
	
//copy files to files folder
function copyFiles ($src, $dst) {
		$handle = opendir($src);
		while ( false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				if (is_dir($src . '/' . $file)) {
					copyFiles($src . '/' . $file, $dst);
				}
				else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($handle);
}

//finally fuck up the column
$sql = "ALTER TABLE cms".WCF_N."_file DROP filename";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();

//link pages & stylesheets
$pages = new PageList();
$pages->readObjects();
foreach ($pages->getObjects() as $page) {
	if ($page->stylesheets != '') {
		$styleIDs = @unserialize($page->stylesheets);
		foreach ($styleIDs as $styleID) {
			$sql = "INSERT INTO cms".WCF_N."_stylesheet_to_page VALUES(?,?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($styleID, $page->pageID));
		}
	}
}
//drop column
$sql = "ALTER TABLE cms".WCF_N."_page DROP stylesheets";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
