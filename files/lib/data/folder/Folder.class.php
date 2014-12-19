<?php
namespace cms\data\folder;

use cms\data\file\FileList;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a folder.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Folder extends DatabaseObject implements IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'folder';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'folderID';

	/**
	 * Returns all files within this folder. Set the first parameter $type
	 * to 'image' to only fetch images.
	 * 
	 * @param	string		$type
	 * @return	array<\cms\data\file\File>
	 */
	public function getFiles($type = '') {
		$fileList = new FileList();

		if ($type == 'image') {
			$fileList->getConditionBuilder()->add('file.type LIKE ?', array('image/%'));
		}

		$fileList->getConditionBuilder()->add('folderID = ?', array($this->folderID));
		$fileList->readObjects();

		return $fileList->getObjects();
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->folderName);
	}
}
