<?php
namespace cms\data\stylesheet;

use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;

/**
 * Represents a stylesheet.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Stylesheet extends DatabaseObject implements IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'stylesheet';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'sheetID';

	/**
	 * Returns the title of this stylesheet.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->title;
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
}
