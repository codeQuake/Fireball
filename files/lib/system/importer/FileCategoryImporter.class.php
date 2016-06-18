<?php

namespace cms\system\importer;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCategoryImporter;

class FileCategoryImporter extends AbstractCategoryImporter {
	/**
	 * @see	\wcf\system\importer\AbstractCategoryImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.codequake.cms.file';
	
	/**
	 * Initializes the file category importer.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.file', $this->objectTypeName);
		$this->objectTypeID = $objectType->objectTypeID;
	}
}
