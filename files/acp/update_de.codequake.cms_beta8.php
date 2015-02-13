<?php
use cms\data\file\FileEditor;
use cms\data\file\FileList;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015-2015 codeQuake
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

//get files into basic category
$list = new FileList();
$list->readObjects();
foreach ($list->getObjects() as $file) {
	$sql = "INSERT INTO cms".WCF_N."_file_to_category VALUES (?, ?)";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute(array($file->fileID, $categoryID));
}


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
