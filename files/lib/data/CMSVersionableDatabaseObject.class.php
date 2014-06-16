<?php
namespace cms\data;

use wcf\data\VersionableDatabaseObject;

/**
 * Abstract database object implementation for cms versionable database objects.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class CMSVersionableDatabaseObject extends VersionableDatabaseObject {
	/**
	 * @see	\wcf\data\IStorableObject::getDatabaseTableName()
	 */
	public static function getDatabaseTableName() {
		return 'cms' . WCF_N . '_' . static::$databaseTableName;
	}
}
