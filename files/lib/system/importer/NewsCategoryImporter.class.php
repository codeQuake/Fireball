<?php
namespace cms\system\importer;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
 
class NewsCategoryImporter extends AbstractImporter {

	protected $className = 'cms\data\category\NewsCategory';

	public function import($oldID, array $data, array $additionalData = array()) {
		// receive objectTypeID
		$data['objectTypeID'] = CategoryHandler::getInstance()->getObjectTypeByName('de.codequake.cms.category.news')->objectTypeID;


		if (isset($additionalData['parentCategoryID'])) {
			$data['parentCategoryID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.category.news', $additionalData['parentCategoryID']);
		}

		if (!isset($data['parentCategoryID']) || $data['parentCategoryID'] == null || !is_numeric($data['parentCategoryID'])) {
			$data['parentCategoryID'] = 0;
		}

		$action = new CategoryAction(array(), 'create', array(
			'data' => $data
		));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->categoryID;


		ImportHandler::getInstance()->saveNewID('de.codequake.cms.category.news', $oldID, $newID);

		return $newID;
	}
}
