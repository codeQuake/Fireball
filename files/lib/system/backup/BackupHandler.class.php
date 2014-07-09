<?php
namespace cms\system\backup;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\PageCacheBuilder;
use cms\data\stylesheet\StylesheetList;
use cms\data\layout\LayoutList;
use wcf\system\SingletonFactory;
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

	protected function init() {
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		$this->contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		$list = new StylesheetList();
		$list->readObjects();
		$this->stylesheets = $list->getObjects();
		$list = new LayoutList();
		$list->readObjects();
		$this->layouts = $list->getObjects();
	}

	public function buildXML() {

		//start doc
		$xml = new XMLWriter();
		$xml->beginDocument('data', '', '');
		$objects = array('page', 'content', 'stylesheet', 'layout');

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
}
