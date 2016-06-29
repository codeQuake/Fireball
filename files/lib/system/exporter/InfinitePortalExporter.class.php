<?php

namespace cms\system\exporter;
use cms\data\page\Page;
use cms\util\PageUtil;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\PackageCache;
use wcf\data\DatabaseObject;
use wcf\system\database\DatabaseException;
use wcf\system\exporter\AbstractExporter;
use wcf\system\importer\ImportHandler;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides an exporter from Infinite Portal 1.1 into Fireball CMS 2.1
 *
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class InfinitePortalExporter extends AbstractExporter {
	/**
	 * full wsip installation number
	 * @var	integer
	 */
	protected $dbNo = 0;
	
	/**
	 * wsip installation number
	 * @var	integer
	 */
	protected $wsipNo = 0;
	
	/**
	 * wcf installation number
	 * @var	integer
	 */
	protected $wcfNo = 0;
	
	/**
	 * @see \wcf\system\exporter\AbstractExporter::$methods
	 */
	protected $methods = array(
		'de.codequake.cms.page' => 'Pages',
		'de.codequake.cms.content' => 'Contents'
	);
	
	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$limits
	 */
	protected $limits = array(
		'de.codequake.cms.page' => 300,
		'de.codequake.cms.content' => 100
	);
	
	private $availableLanguages = array();
	
	private $oldLanguages = array();

	private $pages = array();
	
	private $contents = array();
	
	/**
	 * @see	\wcf\system\exporter\IExporter::init()
	 */
	public function init() {
		parent::init();
		
		if (preg_match('/^wsip(\d+)_(\d+)_$/', $this->databasePrefix, $match)) {
			$this->dbNo = $match[1] . "_" . $match[2];
			$this->wcfNo = $match[1];
			$this->wsipNo = $match[2];
		}
		
		$this->availableLanguages = LanguageFactory::getInstance()->getLanguages();
		
		$this->getOldLanguages();
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::validateFileAccess()
	 */
	public function validateFileAccess() {
		if (in_array('de.codequake.cms.file', $this->selectedData)) {
			if (empty($this->fileSystemPath) || !@file_exists($this->fileSystemPath.'lib/core.functions.php')) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::getSupportedData()
	 */
	public function getSupportedData() {
		return array(
			'de.codequake.cms.page' => array(
				'de.codequake.cms.content'
			)
		);
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::validateDatabaseAccess()
	 */
	public function validateDatabaseAccess() {
		parent::validateDatabaseAccess();
		
		$sql = "SELECT	packageID, packageDir, packageVersion
			FROM	wcf".$this->wcfNo."_package
			WHERE	package = ?";
		$statement = $this->database->prepareStatement($sql, 1);
		$statement->execute(array('com.wcfsolutions.wsip'));
		$row = $statement->fetchArray();
		
		if ($row !== false) {
			if (substr($row['packageVersion'], 0, 1) != 1)
				throw new DatabaseException('Cannot find Infinite Portal 1.1 installation', $this->database);
		} else {
			throw new DatabaseException('Cannot find Infinite Portal installation', $this->database);
		}
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::getQueue()
	 */
	public function getQueue() {
		$queue = array();
		
		if (in_array('de.codequake.cms.page', $this->selectedData)) {
			$queue[] = 'de.codequake.cms.page';
			
			if (in_array('de.codequake.cms.content', $this->selectedData))
				$queue[] = 'de.codequake.cms.content';
		}
		
		return $queue;
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::getDefaultDatabasePrefix()
	 */
	public function getDefaultDatabasePrefix() {
		return 'wsip1_1_';
	}
	
	/**
	 * Counts pages.
	 */
	public function countPages() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wsip" . $this->dbNo . "_content_item";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports pages.
	 */
	public function exportPages($offset, $limit) {
		$sql = "SELECT	*
			FROM	wsip" . $this->dbNo . "_content_item
			ORDER BY	contentItemID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$this->pages[$row['parentID']][] = $row;
		}
		
		$this->exportPagesRecursively();
	}
	
	protected function exportPagesRecursively($parentID = 0) {
		if (!isset($this->pages[$parentID])) return;
		
		foreach ($this->pages[$parentID] as $row) {
			$additionalData = array();
			
			foreach ($this->availableLanguages as $lang) {
				if (!empty($this->oldLanguages[$lang->languageCode]))
					$titleValues[$lang->languageID] = $this->getLangItem('wsip.contentItem.' . $row['contentItem'], $this->oldLanguages[$lang->languageCode]['languageID']);
				else
					$titleValues[$lang->languageID] = $this->getLangItem('wsip.contentItem.' . $row['contentItem'], $this->oldLanguages['default']['languageID']);
			}
			
			I18nHandler::getInstance()->setValues('title', $titleValues);
			
			// prefer english aliases, otherwise use default language
			$language = LanguageFactory::getInstance()->getLanguageByCode('en');
			if ($language === null) {
				$language = LanguageFactory::getInstance()->getLanguage(LanguageFactory::getInstance()->getDefaultLanguageID());
			}
			
			$alias = PageUtil::buildAlias($titleValues[$language->languageID]);
			
			$additionalDataColumn = array();
			if ($row['contentItemType'] == 1) {
				$pageObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.link');
				$additionalDataColumn['url'] = $row['externalURL'];
				$additionalDataColumn['delayedRedirect'] = 1;
			} else {
				$pageObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.page');
			}
			
			$pageID = ImportHandler::getInstance()->getImporter('de.codequake.cms.page')->import($row['contentItemID'], array(
				'showOrder' => $row['showOrder'],
				'parentID' => $row['parentID'],
				//'styleID' => $row['styleID'], //TODO
				'alias' => $alias,
				'allowIndexing' => $row['allowSpidersToIndexThisPage'],
				'publicationDate' => $row['publishingStartTime'],
				'deactivationDate' => $row['publishingEndTime'],
				'objectTypeID' => $pageObjectType->objectTypeID,
				'metaKeywords' => $this->getLangItem('wsip.contentItem.' . $row['contentItem'] . '.metaKeywords', $this->oldLanguages['default']['languageID']),
				'metaDescription' => $this->getLangItem('wsip.contentItem.' . $row['contentItem'] . '.metaDescription', $this->oldLanguages['default']['languageID']),
				'additionalData' => serialize($additionalDataColumn)
			), $additionalData);
			
			if ($pageID) {
				$this->saveI18nValue(new Page($pageID), 'page', 'title');
			}
			
			$this->exportPagesRecursively($row['contentItemID']);
		}
	}
	
	/**
	 * Counts contents (pages, boxes, etc).
	 */
	public function countContents() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wsip" . $this->dbNo . "_content_item";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports contents (pages, boxes, etc).
	 */
	public function exportContents($offset, $limit) {
		$sql = "SELECT	*
			FROM	wsip" . $this->dbNo . "_content_item
			ORDER BY	contentItemID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$additionalData = array();
			
			if ($row['contentItemType'] == 1) {
				// external link >> do nothing
			} else if ($row['contentItemType'] == 2) {
				// box based page
				$sql = "SELECT	item_box.*
					FROM	wsip" . $this->dbNo . "_content_item_box item_box,
						wcf" . $this->wcfNo . "_box box
					WHERE	item_box.contentItemID = ?
					ORDER BY	item_box.showOrder";
				$boxListStatement = $this->database->prepareStatement($sql);
				$boxListStatement->execute(array($row['contentItemID']));
				
				while ($box = $boxListStatement->fetchArray()) {
					$sql = "SELECT	*
						FROM	wcf" . $this->wcfNo . "_box_tab tab
						WHERE	tab.boxID = ?
							AND item_box.boxID = box.boxID
						ORDER BY	showOrder";
					$tabListStatement = $this->database->prepareStatement($sql);
					$tabListStatement->execute(array($box['boxID']));
					
					$contentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.tabmenu');
					$contentID = ImportHandler::getInstance()->getImporter('de.codequake.cms.content')->import('b' . $box['boxID'], array(
						'pageID' => $row['contentItemID'],
						'title' => '',
						'contentTypeID' => $contentObjectType->objectTypeID,
						'contentData' => array(),
						'showOrder' => $box['showOrder']
					), $additionalData);
					
					while ($tab = $tabListStatement->fetchArray()) {
						$sql = "SELECT option_table.*, value_table.*
							FROM	wcf" . $this->wcfNo . "_box_tab_option option_table,
								wcf" . $this->wcfNo . "_box_tab_option_value value_table
							WHERE	value_table.boxTabID = ?
								AND option_table.optionID = value_table.optionID
								AND option_table.boxTabType = ?";
						$optionStatement = $this->database->prepareStatement($sql);
						$optionStatement->execute(array($tab['boxTabID'], $tab['boxTabType']));
						$options = array();
						while ($option = $optionStatement->fetchArray()) {
							$options[$option['optionName']] = $option;
						}
						
						$contentData = array();
						if ($tab['boxTabType'] == 'content') {
							$contentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.wsipimport');
							$contentData['text'] = $this->getLangItem($options['text']['optionValue'], $this->oldLanguages['default']['languageID']);
						} else if ($tab['boxTabType'] == 'html') {
							$contentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.template');
							$contentData['text'] = $options['htmlCode']['optionValue'];
						} else if ($tab['boxTabType'] == 'contentItems') {
							$contentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.menu');
							$contentData['type'] = 'children';
							$contentData['pageID'] = $options['contentItems']['optionValue'];
						} else {
							// can't import other types without problems
							continue;
						}
						
						$contentTabID = ImportHandler::getInstance()->getImporter('de.codequake.cms.content')->import('t' . $tab['boxTabID'], array(
							'pageID' => $row['contentItemID'],
							'title' => $this->getLangItem('wcf.box.tab.' . $tab['boxTab'], $this->oldLanguages['default']['languageID']),
							'contentTypeID' => $contentObjectType->objectTypeID,
							'contentData' => $contentData,
							'showOrder' => $tab['showOrder'],
							'parentID' => $contentID,
							'dontUpdateParentID' => true
						), $additionalData);
					}
				}
			} else {
				// html content
				$contentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.wsipimport');
				$contentID = ImportHandler::getInstance()->getImporter('de.codequake.cms.content')->import($row['contentItemID'], array(
					'pageID' => $row['contentItemID'],
					'title' => '',
					'contentTypeID' => $contentObjectType->objectTypeID,
					'contentData' => array(
						'text' => $this->getLangItem('wsip.contentItem.' . $row['contentItem'] . '.text', $this->oldLanguages['default']['languageID'])
					),
					//'cssClasses' => 'container containerPadding marginTop' //TODO
				), $additionalData);
			}
		}
	}
	
	private function getLangItem($langItem, $languageID = 1) {
		$sql = "SELECT	*
			FROM	wcf" . $this->wcfNo . "_language_item
			WHERE	languageItem = ?
				AND languageID = ?
			ORDER BY	languageItemID";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($langItem, $languageID));
		
		$row = $statement->fetchSingleRow();
		
		if ($row['languageUseCustomValue']) {
			if (!empty($row['languageCustomItemValue']))
				return $row['languageCustomItemValue'];
			else if (!empty($row['languageItemValue']))
				return $row['languageItemValue'];
		} else {
			if (!empty($row['languageItemValue']))
				return $row['languageItemValue'];
		}
		
		return '';
	}
	
	private function saveI18nValue(DatabaseObject $object, $type, $columnName) {
		$application = 'cms';
		if ($type == 'category')
			$application = 'wcf';
		
		if (!I18nHandler::getInstance()->isPlainValue($columnName)) {
			I18nHandler::getInstance()->save(
				$columnName,
				$application.'.'.$type.'.'.$columnName. $object->{$type.'ID'},
				$application.'.'.$type,
				PackageCache::getInstance()->getPackageID('de.codequake.cms')
			);
			
			$editorName = '\\'.$application.'\\data\\'.$type.'\\'.ucfirst($type).'Editor';
			
			if ($object !== null) {
				$editor = new $editorName($object);
				
				if ($type == 'content' && $columnName == 'text') {
					$tmpContentData = $object->contentData;
					
					if ($this->is_serialized($tmpContentData)) {
						$tmpContentData = unserialize($tmpContentData);
						$tmpContentData['text'] = $application.'.'.$type.'.'.$columnName. $object->{$type.'ID'};
					} else if (is_array($tmpContentData)) {
						$tmpContentData['text'] = $application.'.'.$type.'.'.$columnName. $object->{$type.'ID'};
					} else {
						$tmpContentData = array();
						$tmpContentData['text'] = $application.'.'.$type.'.'.$columnName. $object->{$type.'ID'};
					}
					
					$tmpContentData = serialize($tmpContentData);
					
					$editor->update(array(
						'contentData' => $tmpContentData
					));
				} else {
					$editor->update(array(
						$columnName => $application.'.'.$type.'.'.$columnName. $object->{$type.'ID'}
					));
				}
			}
		}
	}
	
	private function getOldLanguages() {
		$sql = "SELECT *
			FROM	wcf" . $this->wcfNo . "_language";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$this->oldLanguages[$row['languageCode']] = $row;
			
			if (intval($row['isDefault']) == 1) {
				$this->oldLanguages['default'] = $row;
			}
		}
	}
}
