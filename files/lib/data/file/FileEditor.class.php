<?php
namespace cms\data\file;

use cms\system\cache\builder\FileCacheBuilder;
use cms\system\cache\builder\FilePermissionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;

/**
 * Functions to edit a file.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = File::class;

	/**
	 * @inheritDoc
	 */
	public static function deleteAll(array $objectIDs = []) {
		$fileList = new FileList();
		$fileList->setObjectIDs($objectIDs);
		$fileList->readObjects();

		foreach ($fileList as $object) {
			$fileEditor = new FileEditor($object);
			$fileEditor->deleteFile();
		}

		return parent::deleteAll($objectIDs);
	}

	/**
	 * Deletes the actual file.
	 */
	public function deleteFile() {
		if (file_exists($this->getLocation())) {
			@unlink($this->getLocation());
		}
		if (file_exists($this->getThumbnailLocation())) {
			@unlink($this->getThumbnailLocation());
		}
	}

	/**
	 * Updates category ids.
	 * 
	 * @param	array<integer>		$categoryIDs
	 */
	public function updateCategoryIDs(array $categoryIDs = []) {
		// remove old assigns
		$sql = "DELETE FROM	cms".WCF_N."_file_to_category
			WHERE		fileID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->fileID]);

		// new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();

			$sql = "INSERT INTO	cms".WCF_N."_file_to_category
						(categoryID, fileID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute([$categoryID, $this->fileID]);
			}

			WCF::getDB()->commitTransaction();
		}
	}

	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		FileCacheBuilder::getInstance()->reset();
		FilePermissionCacheBuilder::getInstance()->reset();
	}
}
