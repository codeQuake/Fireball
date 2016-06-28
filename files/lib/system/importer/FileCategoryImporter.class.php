<?php

namespace cms\system\importer;
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCategoryImporter;
use wcf\system\importer\ImportHandler;

class FileCategoryImporter extends AbstractCategoryImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'wcf\data\category\Category';
	
	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		if (!empty($data['parentCategoryID'])) $data['parentCategoryID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.file.category', $data['parentCategoryID']);
		
		$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', 'de.codequake.cms.file')->objectTypeID;
		$category = CategoryEditor::create(array_merge($data, array('objectTypeID' => $objectTypeID)));
		
		ImportHandler::getInstance()->saveNewID('de.codequake.cms.file.category', $oldID, $category->categoryID);
		
		return $category->categoryID;
	}
}
