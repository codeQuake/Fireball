<?php
namespace cms\data;
use wcf\data\DatabaseObject;

class FireballDatabaseObject extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	public static function getDatabaseTableName() {
		$className = get_called_class();
		$classParts = explode('\\', $className);

		if (static::$databaseTableName !== '') {
			return $classParts[0].WCF_N.'_'.static::$databaseTableName;
		}

		static $databaseTableName = null;
		if ($databaseTableName === null) {
			if ($classParts[0] == 'cms')
				$databaseTableName = 'fireball'.WCF_N.'_'.strtolower(implode('_', preg_split('~(?=[A-Z](?=[a-z]))~', array_pop($classParts), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));
			else
				$databaseTableName = $classParts[0].WCF_N.'_'.strtolower(implode('_', preg_split('~(?=[A-Z](?=[a-z]))~', array_pop($classParts), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));
		}

		return $databaseTableName;
	}
}
