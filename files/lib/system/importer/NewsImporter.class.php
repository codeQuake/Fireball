<?php
namespace cms\system\importer;

use cms\data\news\News;
use cms\data\news\NewsAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\tagging\TagEngine;
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
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
		
		if (! empty($additionalData['languageCode'])) {
			if (($language = LanguageFactory::getInstance()->getLanguageByCode($additionalData['languageCode'])) !== null) {
				$data['languageID'] = $language->languageID;
			}
		}
		
		if (is_numeric($oldID)) {
			$news = new News($oldID);
			if (! $news->newsID) $data['newsID'] = $oldID;
		}
		
		// save categories
		$categoryIDs = array();
		if (! empty($additionalData['categories'])) {
			foreach ($additionalData['categories'] as $oldCategoryID) {
				$newCategoryID = ImportHandler::getInstance()->getNewID('de.codequake.cms.category.news', $oldCategoryID);
				if ($newCategoryID) $categoryIDs[] = $newCategoryID;
			}
		}
		
		if (empty($categoryIDs)) {
			$categoryIDs[] = $this->getImportCategoryID();
		}
		
		$action = new NewsAction(array(), 'create', array(
			'data' => $data,
			'categoryIDs' => $categoryIDs
		));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->entryID;
		
		$news = new News($newID);
		
		// save tags
		if (! empty($additionalData['tags'])) {
			TagEngine::getInstance()->addObjectTags('de.codequake.cms.news', $news->newsID, $additionalData['tags'], ($news->languageID ?  : LanguageFactory::getInstance()->getDefaultLanguageID()));
		}
		
		ImportHandler::getInstance()->saveNewID('de.codequake.cms.news', $oldID, $news->newsID);
		return $news->newsID;
	}

	private function getImportCategoryID() {
		if (! $this->importCategoryID) {
			$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.category', 'de.codequake.cms.category.news');
			
			$sql = "SELECT		categoryID
				FROM		wcf" . WCF_N . "_category
				WHERE		objectTypeID = ?
						AND parentCategoryID = ?
						AND title = ?
				ORDER BY	categoryID";
			$statement = WCF::getDB()->prepareStatement($sql, 1);
			$statement->execute(array(
				$objectTypeID,
				0,
				'Import'
			));
			$row = $statement->fetchArray();
			if ($row !== false) {
				$this->importCategoryID = $row['categoryID'];
			}
			else {
				$sql = "INSERT INTO	wcf" . WCF_N . "_category
							(objectTypeID, parentCategoryID, title, showOrder, time)
					VALUES		(?, ?, ?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
					$objectTypeID,
					0,
					'Import',
					0,
					TIME_NOW
				));
				$this->importCategoryID = WCF::getDB()->getInsertID("wcf" . WCF_N . "_category", 'categoryID');
			}
		}
		
		return $this->importCategoryID;
	}
}
