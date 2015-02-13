<?php
namespace cms\data;

use wcf\data\DatabaseObject;

/**
 * Abstract database object implementation for cms database objects.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class CMSDatabaseObject extends DatabaseObject {
	/**
	 * @see	\wcf\data\IStorableObject::getDatabaseTableName()
	 */
	public static function getDatabaseTableName() {
		return 'cms' . WCF_N . '_' . static::$databaseTableName;
	}
}
