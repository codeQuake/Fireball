<?php
namespace cms\system\backup;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\PageCacheBuilder;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use cms\data\layout\LayoutList;
use cms\data\stylesheet\StylesheetList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\io\TarWriter;
use wcf\system\SingletonFactory;
use wcf\util\DirectoryUtil;
use wcf\util\StringUtil;
use wcf\util\XMLWriter;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class BackupHandler extends SingletonFactory{

	public $objects = array('page', 'content', 'stylesheet', 'layout','file', 'folder');
	protected $pages = null;
	protected $contents = null;
	protected $layouts = null;
	protected $stylesheets = null;
	protected $files = null;
	protected $folders = null;

	protected  $filename = '';
	protected $data;

	protected function init() {
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');

		$list = new StylesheetList();
		$list->readObjects();
		$this->stylesheets = $list->getObjects();

		$list = new LayoutList();
		$list->readObjects();
		$this->layouts = $list->getObjects();

		$list = new FolderList();
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

		//start doc
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

		//end doc
		$xml->endDocument(CMS_DIR . 'export/cmsData.xml');
	}

	protected function tar() {
		$this->filename = CMS_DIR . 'export/CMS-Export.' . StringUtil::getRandomID() . '.tgz';
		$this->buildXML();
		$this->tarFiles();
		$files = array('cmsData.xml', 'files.tar');

		$tar = new TarWriter($this->filename, true);
		foreach ($files as $file) {
			$tar->add(CMS_DIR . 'export/'.$file, '', CMS_DIR . 'export/');
			@unlink(CMS_DIR . 'export/'.$file);
		}
		$tar->create();
	}

	protected function tarFiles() {
		$files = new DirectoryUtil(CMS_DIR . 'files/');
		$tar = new TarWriter(CMS_DIR . 'export/files.tar');
		$tar->add($files->getFiles(), '', CMS_DIR . 'files/');
		$tar->create();
	}

	public function handleImport($filename) {
		//clean all
		foreach ($this->objects as $object) {
			$actionName = ucfirst($object).'Action';
			$action = new $actionName($this->{$object.'s'}, 'delete');
			$action->executeAction();
		}

		if (file_exists(CMS_DIR . 'files/')) {
			DirectoryUtil::getInstance(CMS_DIR . 'files/')->removeAll();
		}

		$this->openTar($filename);

		//import all
		foreach ($this->objects as $object) {
			if (isset($this->data[$object.'s'])) {
				foreach ($this->data[$object.'s'] as $$object) {
					$actionName = ucfirst($object).'Action';
					$action = new $actionName(array(), 'create', array('data' => $$object));
					$action->executeAction();
				}
			}
		}
	}

	protected function openTar($filename) {
		$tar = new Tar($filename);
		$this->data = $this->readXML($tar);
		$this->importFiles();
		$tar->close();
	}

	protected function importFiles() {
		$files = 'files.tar';
		if ($tar->getIndexByFileName($files) === false) {
			throw new SystemException("Unable to find required file '" . $files . "' in the import archive");
		}
		$tar->extract($files, CMS_DIR . 'files/files.tar');

		$ftar = new Tar(CMS_DIR . 'files/files.tar');
		$contentList = $ftar->getContentList();
		foreach ($contentList as $key => $val) {
			if ($val['type'] == 'file' && $val['filename'] != '/files.tar' && $val['filename'] != 'files.tar') $ftar->extract($key, CMS_DIR . 'files/' . $val['filename']);
			else if (! file_exists(CMS_DIR . 'files/' . $val['filename'])) mkdir(CMS_DIR . 'files/' . $val['filename']);
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
		foreach ($items as $item) {
			foreach ($xpath->query('child::*', $item) as $child) {
				foreach ($xpath->query('child::*', $item) as $property) {
					if ($property->tagName == 'contentTypeID') $data[$item->tagName][][$property->tagName] = ObjectTypeCache::getInstance()->getObjectTypeByName($property->nodeValue)->objectType;
					else $data[$item->tagName][][$property->tagName] = $property->nodeValue;
				}
			}
		}

		return $data;
	}
}

