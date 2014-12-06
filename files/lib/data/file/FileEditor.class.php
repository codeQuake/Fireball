<?php
namespace cms\data\file;

use wcf\data\DatabaseObjectEditor;

/**
 * Functions to edit a file.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileEditor extends DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'cms\data\file\File';

	/**
	 * @see	\wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
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
	}
}
