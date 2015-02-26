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
use wcf\system\io\Tar;
use wcf\system\io\TarWriter;
use wcf\system\SingletonFactory;
use wcf\util\DirectoryUtil;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\util\XML;
use wcf\util\XMLWriter;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class BackupHandler extends SingletonFactory{

	public $objects = array('folder', 'file', 'stylesheet', 'page', 'content');
	protected $pages = null;
	protected $contents = null;
	protected $stylesheets = null;
	protected $files = null;
	protected $folders = null;

	protected $filename = '';
	protected $data;
	
	protected $tmp = array('pages' => array(), 'contents' => array(), 'stylesheets' => array(), 'files' => array(), 'folders' => array());
	
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
		// start doc
		$xml = new XMLWriter();
		$xml->beginDocument('data', '', '');

		foreach ($this->objects as $object) {
			if ($this->{$object.'s'} !== null && !empty($this->{$object.'s'})) {
				$xml->startElement($object.'s');
				foreach ($this->{$object.'s'} as $$object) {
					$xml->startElement($object);
					foreach ($$object->getData() as $key => $data) {
						if ($key == 'contentTypeID') $xml->writeElement($key, ObjectTypeCache::getInstance()->getObjectType($data)->objectType);
						else $xml->writeElement($key, $data);
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
					$currentID = $import[$object.'ID'];
					
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
								unset($import['parentID']);
								$parentIDs[$currentID] = $import['parentID'];
							}
						}
					}
					
					// obsolete columns of pages
					if ($object == 'page') {
						if (isset($import['robots'])) unset($import['robots']);
						if (isset($import['showSidebar'])) unset($import['showSidebar']);
						
						if (isset($import['styleID']) && $import['styleID'] == '')
							$import['styleID'] = null;
						
						if (isset($import['authorID'])) $import['authorID'] = null;
						if (isset($import['lastEditorID'])) $import['lastEditorID'] = null;
						
						// save stylesheets
						if (isset($import['stylesheets'])) {
							$upperObjectIDs[$currentID] = unserialize($import['stylesheets']);
							unset($import['stylesheets']);
						}
					}
					
					// obsolete columns for files
					if ($object == 'file') {
						if (isset($import['folderID'])) unset($import['folderID']);
						
						if (isset($import['size'])) {
							$import['filesize'] = $import['size'];
							unset($import['size']);
						}
						
						if (isset($import['type'])) {
							$import['fileType'] = $import['type'];
							unset($import['type']);
						}
						
						// save folders
						if (isset($import['folderID'])) {
							$upperObjectIDs[$currentID] = $import['folderID'];
							unset($import['filename']);
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
					}
					
					// columns for contents
					if ($object == 'content') {
						$import['pageID'] = $this->tmp['pages'][$import['pageID']];
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
						$editorName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Editor';
						$cacheName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Cache';
						
						$newStylesheetIDs = array();
						foreach ($stylesheetIDs as $stylesheet) {
							$newStylesheetIDs[] = $this->tmp['stylesheets'][$stylesheet];
						}
						
						if ($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$pageID]) !== null && !empty($newStylesheetIDs)) {
							$editor = new $editorName($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$pageID]));
							$editor->updateStylesheetIDs($newStylesheetIDs);
						}
					}
				}
				
				// link files and folders
				if ($object == 'file') {
					foreach ($upperObjectIDs as $file => $folder) {
						$editorName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Editor';
						$cacheName = '\cms\data\\'.$object.'\\'.ucfirst($object).'Cache';
						
						if ($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$file]) !== null) {
							$editor = new $editorName($cacheName::getInstance()->{'get'.ucfirst($object)}($this->tmp[$object.'s'][$file]));
							$editor->updateCategoryIDs(array($this->tmp['folders'][$folder]));
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
					if ($property->tagName == 'contentTypeID') $data[$item->tagName][$i][$property->tagName] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.content.type', $property->nodeValue);
					else if ($property->tagName == 'parentID' && $property->nodeValue == '' || $property->tagName == 'menuItemID') $data[$item->tagName][$i][$property->tagName] = null;
					else $data[$item->tagName][$i][$property->tagName] = $property->nodeValue;
				}
				$i++;
			}
		}
		return $data;
	}
}
