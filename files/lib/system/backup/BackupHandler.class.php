<?php
namespace cms\system\backup;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\PageCacheBuilder;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use cms\data\layout\LayoutList;
use cms\data\stylesheet\StylesheetList;
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

	protected $pages = null;
	protected $contents = null;
	protected $layouts = null;
	protected $stylesheets = null;
	protected $files = null;
	protected $folders = null;

	protected  $filename = '';

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
		$objects = array('page', 'content', 'stylesheet', 'layout','file', 'folder');

		foreach ($objects as $object) {
			if ($this->{$object.'s'} !== null && !empty($this->{$object.'s'})) {
				$xml->startElement($object.'s');
				foreach ($this->{$object.'s'} as $$object) {
					$xml->startElement($object);
					foreach ($$object->getData() as $key => $data) {
						$xml->writeElement($key, $data);
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
}
