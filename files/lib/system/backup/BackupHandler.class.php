<?php
namespace cms\system\backup;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\PageCacheBuilder;
use cms\data\content\ContentAction;
use cms\data\file\FileAction;
use cms\data\file\FileList;
use cms\data\page\PageAction;
use cms\data\stylesheet\StylesheetList;
use wcf\data\category\CategoryList;
use wcf\data\category\CategoryNodeTree;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\PackageCache;
use wcf\data\DatabaseObject;
use wcf\system\io\Tar;
use wcf\system\io\TarWriter;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\SingletonFactory;
use wcf\util\DirectoryUtil;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\util\XML;
use wcf\util\XMLWriter;

/**
 * @author	Jens Krumsieck, Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class BackupHandler extends SingletonFactory {

	public $objects = array('folder', 'file', 'stylesheet', 'page', 'content');
	protected $pages = null;
	protected $contents = null;
	protected $stylesheets = null;
	protected $files = null;
	protected $folders = null;

	protected $filename = '';
	protected $data;
	
	protected $tmp = array(
		'pages' => array(),
		'contents' => array(),
		'stylesheets' => array(),
		'files' => array(),
		'folders' => array()
	);
	
	protected $categoryObjectType = 0;

	protected function init() {
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');

		$list = new StylesheetList();
		$list->readObjects();
		$this->stylesheets = $list->getObjects();
		
		$this->categoryObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', 'de.codequake.cms.file');
		
		$list = new CategoryList();
		$list->getConditionBuilder()->add("objectTypeID = ?", array($this->categoryObjectType->objectTypeID));
		$list->readObjects();
		$this->folders = $list->getObjects();

		$list = new FileList();
		$list->readObjects();
		$this->files = $list->getObjects();
	}

	public function getExportArchive() {
		$this->tar();
		return $this->filename;
	}

	protected function buildXML() {
		$cms = PackageCache::getInstance()->getPackage(PackageCache::getInstance()->getPackageID('de.codequake.cms'));
		$cmsVersion = $cms->packageVersion;
		
		// start doc
		$xml = new XMLWriter();
		$xml->beginDocument('data', '', '', array('api' => $cmsVersion));
		
		// get available languages
		$availableLanguages = LanguageFactory::getInstance()->getLanguages();
		
		foreach ($this->objects as $object) {
			if ($this->{$object.'s'} !== null && !empty($this->{$object.'s'})) {
				$xml->startElement($object.'s');
				foreach ($this->{$object.'s'} as $$object) {
					$xml->startElement($object);
					
					$exportData = $$object->getData();
					
					if ($object == 'file') {
						$exportData = array_merge(
							array(
								'categoryIDs' => $$object->getCategoryIDs()
							),
							$exportData
						);
					}
					
					foreach ($exportData as $key => $data) {
						if ($key == 'contentTypeID') {
							$xml->writeElement($key, ObjectTypeCache::getInstance()->getObjectType($data)->objectType);
						} else if (($key == 'title' || $key == 'description' || $key == 'metaDescription' ||
								$key == 'metaKeywords') && $object != 'stylesheet' && $object != 'file') {
							$langData = array();
							
							foreach ($availableLanguages as $lang) {
								$langData[$lang->countryCode] = $lang->get($data);
							}
							
							$xml->writeElement($key, base64_encode(serialize($langData)));
						} else if (is_array($data)) {
							if ($key == 'contentData') {
								$langData = array();
								
								if (isset($data['text'])) {
									foreach ($availableLanguages as $lang) {
										$langData[$lang->countryCode] = $lang->get($data['text']);
									}
									
									$data['text'] = serialize($langData);
								}
							}
							$xml->writeElement($key, base64_encode(serialize($data)));
						} else {
							$xml->writeElement($key, $data);
						}
					}
					
					if ($object == 'page') {
						$stylesheetIDs = $$object->getStylesheetIDs();
						$xml->writeElement('stylesheets', base64_encode(serialize($stylesheetIDs)));
					}
					
					$xml->endElement();
				}
				$xml->endElement();
			}
		}
		
		// end doc
		$xml->endDocument(FileUtil::getTempFolder().'cmsData.xml');
	}

	protected function tar() {
		$this->filename = FileUtil::getTempFolder().'CMS-Export.' . StringUtil::getRandomID() . '.tgz';
		$this->buildXML();
		$this->tarFiles();
		$files = array('cmsData.xml', 'files.tar');
		
		$tar = new TarWriter($this->filename, true);
		foreach ($files as $file) {
			$tar->add(FileUtil::getTempFolder().$file, '', FileUtil::getTempFolder());
			@unlink(FileUtil::getTempFolder().$file);
		}
		$tar->create();
	}

	protected function tarFiles() {
		$files = new DirectoryUtil(CMS_DIR . 'files/');
		$tar = new TarWriter(FileUtil::getTempFolder().'files.tar');
		$tar->add($files->getFiles(), '', CMS_DIR . 'files/');
		
		$tar->create();
	}

	public function handleImport($filename) {
		//clean all
		foreach ($this->objects as $object) {
			if ($object == 'folder') {
				$actionName = '\\wcf\\data\\category\\CategoryAction';
			} else {
				$actionName = '\\cms\data\\'.$object.'\\'.ucfirst($object).'Action';
			}
			$action = new $actionName($this->{$object.'s'}, 'delete');
			$action->executeAction();
		}

		if (file_exists(CMS_DIR . 'files/')) {
			DirectoryUtil::getInstance(CMS_DIR . 'files/')->removeAll();
		}

		// get available languages
		$availableLanguages = LanguageFactory::getInstance()->getLanguages();

		$this->openTar($filename);
		//import all
		foreach ($this->objects as $object) {
			// check if there is something to import 
			if (isset($this->data[$object.'s'])) {
				// temp store parent ids
				$parentIDs = array();
				$upperObjectIDs = array();
				
				// go through every single object
				foreach ($this->data[$object.'s'] as $import) {
					$currentID = (isset($import[$object.'ID']) ? $import[$object.'ID'] : ($object == 'folder' ? $import['categoryID'] : null));
					$langData = array();
					
					// unset current id to be save
					if (isset($import[$object.'ID'])) unset($import[$object.'ID']);
					
					// check parent ids
					if ($object == 'page' || $object == 'content') {
						if (isset($import['parentID']) && $import['parentID'] != '') {
							if (isset($this->tmp[$object.'s'][$import['parentID']])) {
								// get new id for parent, if parent has been already processed
								$import['parentID'] = $this->tmp[$object.'s'][$import['parentID']];
							} else {
								// set when everything is imported
								$parentIDs[$currentID] = $import['parentID'];
								unset($import['parentID']);
							}
						}
					}
					
					// obsolete columns for pages
					if ($object == 'page') {
						if (isset($import['robots'])) unset($import['robots']);
						if (isset($import['showSidebar'])) unset($import['showSidebar']);
						
						if (isset($import['styleID']) && $import['styleID'] == '')
							$import['styleID'] = null;
						
						if (isset($import['authorID'])) $import['authorID'] = null;
						if (isset($import['lastEditorID'])) $import['lastEditorID'] = null;
						
						// save stylesheets
						if (isset($import['stylesheets'])) {
							$tmpStylesheets = base64_decode($import['stylesheets']);
							if ($this->is_serialized($tmpStylesheets)) {
								$tmpStylesheets = unserialize($tmpStylesheets);
								if (!empty($tmpStylesheets))
									$upperObjectIDs[$currentID] = $tmpStylesheets;
							}
							unset($import['stylesheets']);
						}
						
						// multilingual title
						$tmpTitle = base64_decode($import['title']);
						if ($this->is_serialized($tmpTitle)) {
							$tmpTitle = unserialize($tmpTitle);
							
							foreach ($availableLanguages as $lang) {
								if (isset($tmpTitle[$lang->countryCode])) {
									$langData['title'][$lang->languageID] = $tmpTitle[$lang->countryCode];
								} else {
									$langData['title'][$lang->languageID] = '';
								}
							}
							
							$import['title'] = '';
						} else {
							$import['title'] = $tmpTitle;
						}
						
						// multilingual description
						$tmpDescription = base64_decode($import['description']);
						if ($this->is_serialized($tmpDescription)) {
							$tmpDescription = unserialize($tmpDescription);
							
							foreach ($availableLanguages as $lang) {
								if (isset($tmpDescription[$lang->countryCode])) {
									$langData['description'][$lang->languageID] = $tmpDescription[$lang->countryCode];
								} else {
									$langData['description'][$lang->languageID] = '';
								}
							}
							
							$import['description'] = '';
						} else {
							$import['description'] = $tmpDescription;
						}
						
						// multilingual meta description
						$tmpMetaDescription = base64_decode($import['metaDescription']);
						if ($this->is_serialized($tmpMetaDescription)) {
							$tmpMetaDescription = unserialize($tmpMetaDescription);
								
							foreach ($availableLanguages as $lang) {
								if (isset($tmpMetaDescription[$lang->countryCode])) {
									$langData['metaDescription'][$lang->languageID] = $tmpMetaDescription[$lang->countryCode];
								} else {
									$langData['metaDescription'][$lang->languageID] = '';
								}
							}
							
							$import['metaDescription'] = '';
						} else {
							$import['metaDescription'] = $tmpMetaDescription;
						}
						
						// multilingual description
						$tmpMetaKeywords = base64_decode($import['metaKeywords']);
						if ($this->is_serialized($tmpMetaKeywords)) {
							$tmpMetaKeywords = unserialize($tmpMetaKeywords);
								
							foreach ($availableLanguages as $lang) {
								if (isset($tmpMetaKeywords[$lang->countryCode])) {
									$langData['metaKeywords'][$lang->languageID] = $tmpMetaKeywords[$lang->countryCode];
								} else {
									$langData['metaKeywords'][$lang->languageID] = '';
								}
							}
							
							$import['metaKeywords'] = '';
						} else {
							$import['metaKeywords'] = $tmpMetaKeywords;
						}
					}
					
					// obsolete columns for files
					if ($object == 'file') {
						if (isset($import['size'])) {
							$import['filesize'] = $import['size'];
							unset($import['size']);
						}
						
						if (isset($import['type'])) {
							$import['fileType'] = $import['type'];
							unset($import['type']);
						}
						
						// save folders -- compatibility mode
						if (isset($import['folderID'])) {
							$upperObjectIDs[$currentID] = array($import['folderID']);
							unset($import['folderID']);
						}
						if (isset($import['filename'])) unset($import['filename']);
						
						// save folders
						if (isset($import['categoryIDs'])) {
							$tmpCategoryIDs = base64_decode($import['categoryIDs']);
							if ($this->is_serialized($tmpCategoryIDs)) {
								$upperObjectIDs[$currentID] = unserialize($tmpCategoryIDs);
							}
							unset($import['categoryIDs']);
						}
					}
					
					// columns for folders
					if ($object == 'folder') {
						$import['objectTypeID'] = $this->categoryObjectType->objectTypeID;
						
						if (isset($import['folderName'])) {
							$import['title'] = $import['folderName'];
							unset($import['folderName']);
						}
						if (isset($import['folderPath'])) unset($import['folderPath']);
						if (!isset($import['description'])) $import['description'] = '';
						
						// multilingual title
						$tmpTitle = base64_decode($import['title']);
						if ($this->is_serialized($tmpTitle)) {
							$tmpTitle = unserialize($tmpTitle);
							
							foreach ($availableLanguages as $lang) {
								if (isset($tmpTitle[$lang->countryCode])) {
									$langData['title'][$lang->languageID] = $tmpTitle[$lang->countryCode];
								} else {
									$langData['title'][$lang->languageID] = '';
								}
							}
							
							$import['title'] = '';
						} else {
							$import['title'] = $tmpTitle;
						}
						
						// multilingual description
						$tmpDescription = base64_decode($import['description']);
						if ($this->is_serialized($tmpDescription)) {
							$tmpDescription = unserialize($tmpDescription);
								
							foreach ($availableLanguages as $lang) {
								if (isset($tmpDescription[$lang->countryCode])) {
									$langData['description'][$lang->languageID] = $tmpDescription[$lang->countryCode];
								} else {
									$langData['description'][$lang->languageID] = '';
								}
							}
							
							$import['description'] = '';
						} else {
							$import['description'] = $tmpDescription;
						}
					}
					
					// columns for contents
					if ($object == 'content') {
						$import['pageID'] = $this->tmp['pages'][$import['pageID']];
						
						$import['contentData'] = base64_decode($import['contentData']);
						
						// multilingual title
						$tmpTitle = base64_decode($import['title']);
						
						if ($this->is_serialized($tmpTitle)) {
							$tmpTitle = unserialize($tmpTitle);
							
							foreach ($availableLanguages as $lang) {
								if (isset($tmpTitle[$lang->countryCode])) {
									$langData['title'][$lang->languageID] = $tmpTitle[$lang->countryCode];
								} else {
									$langData['title'][$lang->languageID] = '';
								}
							}
							
							$import['title'] = '';
						} else {
							$import['title'] = $tmpTitle;
						}
						
						// multilingual text?
						if ($this->is_serialized($import['contentData'])) {
							$tmpData = unserialize($import['contentData']);
							
							if (isset($tmpData['text'])) {
								$tmpText = $tmpData['text'];
								if ($this->is_serialized($tmpText)) {
									$tmpText = unserialize($tmpText);
									
									foreach ($availableLanguages as $lang) {
										if (isset($tmpText[$lang->countryCode])) {
											$langData['text'][$lang->languageID] = $tmpText[$lang->countryCode];
										} else {
											$langData['text'][$lang->languageID] = '';
										}
									}
									
									$tmpData['text'] = '';
									
									$import['contentData'] = serialize($tmpData);
								}
							}
						}
					}
					
					// get action class name
					if ($object == 'folder') {
						$actionName = '\\wcf\\data\\category\\CategoryAction';
					} else {
						$actionName = '\\cms\data\\'.$object.'\\'.ucfirst($object).'Action';
					}
					
					$action = new $actionName(array(), 'create', array('data' => $import));
					$new = $action->executeAction();
					$new = $new['returnValues'];
					
					// save temp
					if ($object == 'folder')
						$this->tmp[$object.'s'][$currentID] = $new->categoryID;
					else
						$this->tmp[$object.'s'][$currentID] = $new->{$object.'ID'};
					
					// save lang items
					if (!empty($langData)) {
						foreach ($langData as $column => $values) {
							I18nHandler::getInstance()->setValues($column, $values);
							$this->saveI18nValue($new, $object, $column);
						}
					}
				}
				
				// set new parents if needed
				if ($object == 'page' || $object == 'content') {
					foreach ($parentIDs as $child => $parent) {
						$editorName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Editor';
						$cacheName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Cache';
						if ($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$child]) !== null) {
							$editor = new $editorName($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$child]));
							$update['parentID'] = $this->tmp[$object.'s'][$parent];
							$editor->update($update);
						}
					}
				}
				
				// link stylesheets and pages
				if ($object == 'page') {
					foreach ($upperObjectIDs as $pageID => $stylesheetIDs) {
						$editorName = '\\cms\data\\'.$object.'\\'.ucfirst($object).'Editor';
						
						$newStylesheetIDs = array();
						foreach ($stylesheetIDs as $stylesheet) {
							if (isset($this->tmp['stylesheets'][$stylesheet])) $newStylesheetIDs[] = $this->tmp['stylesheets'][$stylesheet];
						}

						$page = new \cms\data\page\Page($this->tmp[$object.'s'][$pageID]);
						if ($page !== null && !empty($newStylesheetIDs)) {
							$editor = new $editorName($page);
							$editor->updateStylesheetIDs($newStylesheetIDs);
						}
					}
				}
				
				// link files and folders
				if ($object == 'file') {
					foreach ($upperObjectIDs as $file => $folders) {
						$editorName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Editor';
						$cacheName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Cache';
						
						$newFolders = array();
						foreach ($folders as $folder) {
							if (isset($this->tmp['folders'][$folder])) $newFolders[] = $this->tmp['folders'][$folder];
						}
						
						if ($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$file]) !== null) {
							$editor = new $editorName($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$file]));
							$editor->updateCategoryIDs($newFolders);
						}
					}
				}
			}
		}
	}

	protected function openTar($filename) {
		$tar = new Tar($filename);
		$this->data = $this->readXML($tar);
		$this->importFiles($tar);
		$tar->close();
	}

	protected function importFiles($tar) {
		$files = 'files.tar';
		if ($tar->getIndexByFileName($files) === false) {
			throw new SystemException("Unable to find required file '" . $files . "' in the import archive");
		}
		$tar->extract($files, CMS_DIR . 'files/files.tar');

		$ftar = new Tar(CMS_DIR . 'files/files.tar');
		$contentList = $ftar->getContentList();
		foreach ($contentList as $key => $val) {
			if ($val['type'] == 'file' && $val['filename'] != '/files.tar' && $val['filename'] != 'files.tar') $ftar->extract($key, CMS_DIR . 'files/' . $val['filename']);
			else if (!file_exists(CMS_DIR . 'files/' . $val['filename'])) mkdir(CMS_DIR . 'files/' . $val['filename']);
		}
		$ftar->close();
		@unlink(CMS_DIR . 'files/files.tar');
	}

	protected function readXML($tar) {
		$xml = 'cmsData.xml';
		if ($tar->getIndexByFileName($xml) === false) {
			throw new SystemException("Unable to find required file '" . $xml . "' in the import archive");
		}
		$xmlData = new XML();
		$xmlData->loadXML($xml, $tar->extractToString($tar->getIndexByFileName($xml)));
		$xpath = $xmlData->xpath();
		$root = $xpath->query('/ns:data')->item(0);
		$items = $xpath->query('child::*', $root);
		$data = array();
		$i = 0;
		foreach ($items as $item) {
			foreach ($xpath->query('child::*', $item) as $child) {
				foreach ($xpath->query('child::*', $child) as $property) {
					if ($property->tagName == 'contentTypeID')
						$data[$item->tagName][$i][$property->tagName] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.content.type', $property->nodeValue);
					else if ($property->tagName == 'parentID' && $property->nodeValue == '' || $property->tagName == 'menuItemID')
						$data[$item->tagName][$i][$property->tagName] = null;
					else
						$data[$item->tagName][$i][$property->tagName] = $property->nodeValue;
				}
				$i++;
			}
		}
		return $data;
	}
	
	private function saveI18nValue(DatabaseObject $object, $type, $columnName) {
		$application = 'cms';
		if ($type == 'folder') {
			$application = 'wcf';
			$type = 'category';
		}
		
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
					} else if(is_array($tmpContentData)) {
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
