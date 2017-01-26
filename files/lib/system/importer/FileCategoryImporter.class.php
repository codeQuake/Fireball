<?php

namespace cms\system\importer;
use wcf\data\category\Category;
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCategoryImporter;
use wcf\system\importer\ImportHandler;

class FileCategoryImporter extends AbstractCategoryImporter {
	/**
	 * @inheritDoc
	 */
	protected $className = Category::class;
	
	/**
	 * @inheritDoc
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		if (!empty($data['parentCategoryID'])) $data['parentCategoryID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.file.category', $data['parentCategoryID']);
		
		$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', 'de.codequake.cms.file')->objectTypeID;
		$category = CategoryEditor::create(array_merge($data, ['objectTypeID' => $objectTypeID]));
		
		ImportHandler::getInstance()->saveNewID('de.codequake.cms.file.category', $oldID, $category->categoryID);
		
		return $category->categoryID;
	}
}
