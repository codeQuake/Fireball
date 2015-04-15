<?php
namespace cms\data\page\revision;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentList;
use wcf\data\object\type\ObjectTypeCache;
use cms\data\page\Page;
use cms\data\page\PageAction;
use wcf\data\package\PackageCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\DatabaseObject;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Executes page revision-related actions.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRevisionAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\page\revision\PageRevisionEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.page.canAddPage');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete', 'restore');

	/**
	 * Validates permissions to restore a specific revision.
	 */
	public function validateRestore() {
		WCF::getSession()->checkPermissions(array('admin.cms.page.canAddPage'));

		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Restores a specific revision.
	 */
	public function restore() {
		// get available languages
		$availableLanguages = LanguageFactory::getInstance()->getLanguages();
		
		$revision = $this->getSingleObject();
		
		WCF::getDB()->beginTransaction();
		
		$pageData = base64_decode($revision->data);
		$pageData = @unserialize($pageData);
		
		$i18nfields = array();
		// save i18n
		foreach ($pageData as $key => $data) {
			if (($key == 'title' || $key == 'description' || $key == 'metaDescription' || $key == 'metaKeywords') && is_array($data)) {
				$langData = array();
				foreach ($availableLanguages as $lang) {
					if (isset($data[$lang->countryCode])) {
						$langData[$lang->languageID] = $data[$lang->countryCode];
					} else {
						$langData[$lang->languageID] = '';
					}
				}
				I18nHandler::getInstance()->setValues($key, $langData);
				$i18nfields[] = $key;
				$pageData[$key] = '';
			}
		}
		
		// restore page
		$pageAction = new PageAction(array($revision->pageID), 'update', array('data' => $pageData));
		$pageAction->executeAction();
		
		// save i18n
		foreach ($pageData as $key => $data) {
			if (($key == 'title' || $key == 'description' || $key == 'metaDescription' || $key == 'metaKeywords') && in_array($key, $i18nfields)) {
				$this->saveI18nValue(new Page($revision->pageID), 'page', $key);
			}
		}
		
		// restore contents
		$contentData = base64_decode($revision->contentData);
		$contentData = @unserialize($contentData);
		
		$contentList = new ContentList();
		$contentList->getConditionBuilder()->add('content.pageID = ?', array($revision->pageID));
		$contentList->readObjects();
		
		$existingContentIDs = $contentList->getObjectIDs();
		$oldContents = array();
		foreach ($contentData as $data) {
			$oldContents[$data['contentID']] = $data;
		}
		
		// delete contents that where created after the revision
		$orphanedElementIDs = array_diff($existingContentIDs, array_keys($oldContents));
		if (!empty($orphanedElementIDs)) {
			$contentAction = new ContentAction($orphanedElementIDs, 'delete');
			$contentAction->executeAction();
		}
		
		foreach ($oldContents as $oldContent) {
			$objectType = ObjectTypeCache::getInstance()->getObjectType($oldContent['contentTypeID']);
			
			if (in_array($oldContent['contentID'], $existingContentIDs)) {
				// content this exists => update
				$i18ntitle = false;
				if (is_array($oldContent['title'])) {
					$langData = array();
					foreach ($availableLanguages as $lang) {
						if (isset($oldContent['title'][$lang->countryCode])) {
							$langData[$lang->languageID] = $oldContent['title'][$lang->countryCode];
						} else {
							$langData[$lang->languageID] = '';
						}
					}
					I18nHandler::getInstance()->setValues($key, $langData);
					$i18ntitle = true;
					$oldContent['title'] = '';
				}
				
				$i18nfields = array();
				foreach ($objectType->getProcessor()->multilingualFields as $field) {
					if (isset($oldContent['contentData'][$field]) && is_array($oldContent['contentData'][$field])) {
						$langData = array();
						foreach ($availableLanguages as $lang) {
							if (isset($oldContent['contentData'][$field][$lang->countryCode])) {
								$langData[$lang->languageID] = $oldContent['contentData'][$field][$lang->countryCode];
							} else {
								$langData[$lang->languageID] = '';
							}
						}
						I18nHandler::getInstance()->setValues($field, $langData);
						$i18nfields[] = $field;
						$oldContent['contentData'][$field] = '';
					}
				}
				
				$contentAction = new ContentAction(array($oldContent['contentID']), 'update', array('data' => $oldContent));
				$contentAction->executeAction();
				
				if ($i18ntitle)
					$this->saveI18nValue(new Content($oldContent['contentID']), 'content', 'title');
				foreach ($objectType->getProcessor()->multilingualFields as $field) {
					if (in_array($field, $i18nfields))
						$this->saveI18nValue(new Content($oldContent['contentID']), 'content', $field, true);
				}
			} else {
				// content was deleted => re-create
				$i18ntitle = false;
				if (is_array($oldContent['title'])) {
					$langData = array();
					foreach ($availableLanguages as $lang) {
						if (isset($oldContent['title'][$lang->countryCode])) {
							$langData[$lang->languageID] = $oldContent['title'][$lang->countryCode];
						} else {
							$langData[$lang->languageID] = '';
						}
					}
					I18nHandler::getInstance()->setValues($field, $langData);
					$i18ntitle = true;
					$oldContent['title'] = '';
				}
				
				$i18nfields = array();
				foreach ($objectType->getProcessor()->multilingualFields as $field) {
					if (isset($oldContent['contentData'][$field])) {
						$langData = array();
						foreach ($availableLanguages as $lang) {
							if (isset($oldContent['contentData'][$field][$lang->countryCode])) {
								$langData[$lang->languageID] = $oldContent['contentData'][$field][$lang->countryCode];
							} else {
								$langData[$lang->languageID] = '';
							}
						}
						I18nHandler::getInstance()->setValues($key, $langData);
						$i18nfields[] = $field;
						$oldContent['contentData'][$field] = '';
					}
				}
				
				$contentAction = new ContentAction(array($oldContent['contentID']), 'create', array('data' => $oldContent));
				$contentAction->executeAction();
				
				if ($i18ntitle)
					$this->saveI18nValue(new Content($oldContent['contentID']), 'content', 'title');
				foreach ($objectType->getProcessor()->multilingualFields as $field) {
					if (in_array($field, $i18nfields))
						$this->saveI18nValue(new Content($oldContent['contentID']), 'content', $field, true);
				}
			}
		}
		
		WCF::getDB()->commitTransaction();
	}
	
	private function saveI18nValue(DatabaseObject $object, $type, $columnName, $inContentData = false) {
		if (!I18nHandler::getInstance()->isPlainValue($columnName)) {
			I18nHandler::getInstance()->save(
					$columnName,
					'cms.'.$type.'.'.$columnName. $object->{$type.'ID'},
					'cms.'.$type,
					PackageCache::getInstance()->getPackageID('de.codequake.cms')
			);
			
			$editorName = '\\cms\\data\\'.$type.'\\'.ucfirst($type).'Editor';
			
			if ($object !== null) {
				$editor = new $editorName($object);
				
				if ($inContentData) {
					$tmpContentData = $object->contentData;
					
					if ($this->is_serialized($tmpContentData)) {
						$tmpContentData = unserialize($tmpContentData);
						$tmpContentData['text'] = 'cms.'.$type.'.'.$columnName. $object->{$type.'ID'};
					} else if (is_array($tmpContentData)) {
						$tmpContentData['text'] = 'cms.'.$type.'.'.$columnName. $object->{$type.'ID'};
					} else {
						$tmpContentData = array();
						$tmpContentData['text'] = 'cms.'.$type.'.'.$columnName. $object->{$type.'ID'};
					}
					
					$tmpContentData = serialize($tmpContentData);
					
					$editor->update(array(
						'contentData' => $tmpContentData
					));
				} else {
					$editor->update(array(
						$columnName => 'cms.'.$type.'.'.$columnName. $object->{$type.'ID'}
					));
				}
			}
		}
	}
	
	private function is_serialized($value, &$result = null) {
		// Bit of a give away this one
		if (!is_string($value))
			return false;
		
		// Serialized false, return true. unserialize() returns false on an
		// invalid string or it could return false if the string is serialized
		// false, eliminate that possibility.
		if ($value === 'b:0;') {
			$result = false;
			return true;
		}
		
		$length	= strlen($value);
		$end	= '';
		
		switch ($value[0]) {
			case 's':
				if ($value[$length - 2] !== '"')
					return false;
			case 'b':
			case 'i':
			case 'd':
				// This looks odd but it is quicker than isset()ing
				$end .= ';';
			case 'a':
			case 'O':
				$end .= '}';
				
				if ($value[1] !== ':')
					return false;
				
				switch ($value[2]) {
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
						break;
						
					default:
						return false;
				}
			case 'N':
				$end .= ';';
				
				if ($value[$length - 1] !== $end[0])
					return false;
				break;
				
			default:
				return false;
		}
		
		if (($result = @unserialize($value)) === false) {
			$result = null;
			return false;
		}
		return true;
	}
}
