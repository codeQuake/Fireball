<?php
namespace cms\system\layout;

use cms\data\page\PageCache;
use cms\data\page\PageList;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LayoutHandler extends SingletonFactory {
	public function getStylesheet($pageID) {
		$filename = RELATIVE_CMS_DIR . 'style/layout-' . $pageID . '.css';
		if (! file_exists($filename)) {
			LayoutCompiler::getInstance()->compile(PageCache::getInstance()->getPage($pageID));
		}
		return '<link rel="stylesheet" type="text/css" href="' . $filename . '" />';
	}

	public function deleteStylesheet($pageID) {
		$filename = RELATIVE_CMS_DIR . 'style/layout-' . $pageID . '.css';
		if (file_exists($filename)) {
			LayoutCompiler::getInstance()->kill(PageCache::getInstance()->getPage($pageID));
		}
	}

	public function deleteStylesheets() {
		$list = new PageList();
		$list->readObjects();
		foreach ($list->getObjects() as $page) {
			$this->deleteStylesheet($page->pageID);
		}
	}
}
