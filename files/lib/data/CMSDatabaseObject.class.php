<?php
namespace cms\data;

use wcf\data\DatabaseObject;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package de.codequake.cms
 *         
 */
abstract class CMSDatabaseObject extends DatabaseObject {

    /**
     *
     * @see wcf\data\IStorableObject::getDatabaseTableName()
     *
     */
    public static function getDatabaseTableName() {
        return 'cms' . WCF_N . '_' . static::$databaseTableName;
    }
}
