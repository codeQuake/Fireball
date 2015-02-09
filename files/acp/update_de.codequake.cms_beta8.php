<?php
use cms\data\file\FileList;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014-2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

//add default category
$objectType = CategoryHandler::getInstance()->getObjectTypeByName('de.codequake.cms.file');
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
$categoryID = $returnValues['returnValues']->categoryID;

//move files out of their old subfolders
$sql = "SELECT * FROM cms".WCF_N."_folder";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
$files = array();
while ($row = $statement->fetchArray()) {
	$dir = opendir(CMS_DIR.'/files/'.$row['folderPath']);
	while (($file = readdir($dir)) !== false) {
		if ($file != '.' && $file != '..') {
			@copy($dir.'/'.$file, CMS_DIR.'/files/'.$file);
		}
	}
	closedir($dir);
	@unlink($dir);
}

//get files into basic category
$list = new FileList();
$list->readObjects();
foreach ($list->getObjects as $file) {
	$sql = "INSERT INTO cms".CMS_N."_file_to_category VALUES (?, ?)";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute(array($file->fileID, $categoryID));
}
