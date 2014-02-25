<?php
namespace cms\system\importer;
use cms\data\news\NewsAction;
use cms\data\news\NewsList;
use wcf\data\category\CategoryAction;
use wcf\system\category\CategoryHandler;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
 
class NewsImporter extends AbstractImporter {

	protected $className = 'cms\data\news\News';


	private $importCategoryID = 0;

	public function import($oldID, array $data, array $additionalData = array()) {
		// get category
		$categoryID = ImportHandler::getInstance()->getNewID('de.codequake.cms.category.news', $additionalData['categoryID']);


		$tags = '';
		if (isset($additionalData['tags']) && is_array($additionalData['tags'])) {
			$tags = $additionalData['tags'];
		}

		if ($categoryID == null || !is_numeric($categoryID)) {
			$categoryID = $this->getImportCategoryID();
		}

		// handle language
		if (isset($additionalData['languageCode']) && !empty($additionalData['languageCode'])) {
			if (($language = LanguageFactory::getInstance()->getLanguageByCode($additionalData['languageCode'])) !== null) {
				$data['languageID'] = $language->languageID;
			}
		}

		// userID
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);

		$action = new NewsAction(array(), 'create', array(
			'data' => $data,
			'categoryIDs' => array($categoryID),
			'tags' => $tags
		));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->entryID;

		ImportHandler::getInstance()->saveNewID('de.codequake.cms.news', $oldID, $newID);

		return $newID;
	}

	

	private function getImportCategoryID() {
		if (!$this->importCategoryID) {
			$objectTypeID = CategoryHandler::getInstance()->getObjectTypeByName('de.codequake.cms.category.news')->objectTypeID;

			$sql = "SELECT		categoryID
				FROM		wcf".WCF_N."_category
				WHERE		objectTypeID = ?
						AND parentCategoryID = ?
						AND title = ?
				ORDER BY	categoryID";
			$statement = WCF::getDB()->prepareStatement($sql, 1);
			$statement->execute(array($objectTypeID, 0, 'Import'));
			$row = $statement->fetchArray();
			if ($row !== false) {
				$this->importCategoryID = $row['categoryID'];
			} else {
				$action = new CategoryAction(array(), 'create', array(
					'data' => array(
						'objectTypeID' => $objectTypeID,
						'parentCategoryID' => 0,
						'title' => 'Import',
						'showOrder' => 0,
						'time' => TIME_NOW
					)
				));
				$returnValues = $action->executeAction();
				$this->importCategoryID = $returnValues['returnValues']->categoryID;
			}
		}

		return $this->importCategoryID;
	}
}
