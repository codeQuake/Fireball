<?php
namespace cms\data\module;

use cms\data\CMSDatabaseObject;
use wcf\data\template\Template;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a module.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Module extends CMSDatabaseObject implements IRouteController {

	protected static $databaseTableName = 'module';

	protected static $databaseTableIndexName = 'moduleID';

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
		return $this->moduleTitle;
	}
}
