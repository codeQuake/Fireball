<?php
namespace cms\data\folder;

use cms\data\file\FileList;
use cms\data\CMSDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class Folder extends CMSDatabaseObject implements IRouteController {
	protected static $databaseTableName = 'folder';
	protected static $databaseTableIndexName = 'folderID';

	public function __construct($id, $row = null, $object = null) {
		if ($id !== null) {
			$sql = "SELECT *
                    FROM " . static::getDatabaseTableName() . "
                    WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$id
			));
			$row = $statement->fetchArray();
			
			if ($row === false) $row = array();
		}
		
		parent::__construct(null, $row, $object);
	}

	public function getTitle() {
		return $this->folderName;
	}

	public function getFiles($type = '') {
		$list = new FileList();
		if ($type == 'image') $list->getConditionBuilder()->add('file.type LIKE ?', array(
			'image/%'
		));
		$list->getConditionBuilder()->add('folderID = ?', array(
			$this->folderID
		));
		$list->readObjects();
		return $list->getObjects();
	}
}
