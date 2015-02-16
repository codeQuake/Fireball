<?php
namespace cms\data\file;

use wcf\system\WCF;

/**
 * Represents a list of files that are assigned to one of the specified
 * categories.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CategoryFileList extends FileList {
	/**
	 * Creates a new CategoryFileList object.
	 * 
	 * @param	array<integer>		$categoryIDs
	 * @see	\wcf\data\DatabaseObjectList::__construct()
	 */
	public function __construct(array $categoryIDs) {
		parent::__construct();

		$this->sqlJoins .= " LEFT JOIN cms".WCF_N."_file_to_category file_to_category ON (file.fileID = file_to_category.fileID)";

		if (!empty($categoryIDs)) {
			$this->getConditionBuilder()->add('file_to_category.categoryID IN (?)', array($categoryIDs));
			$this->getConditionBuilder()->add('file.fileID = file_to_category.fileID');
		} else {
			$this->getConditionBuilder()->add('1=0');
		}
	}

	/**
	 * @see	\wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms".WCF_N."_file_to_category file_to_category,
				cms".WCF_N."_file file
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		return $statement->fetchColumn();
	}
}
