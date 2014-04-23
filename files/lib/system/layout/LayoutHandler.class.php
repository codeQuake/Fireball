<?php
namespace cms\system\layout;

use cms\data\layout\Layout;
use cms\data\layout\LayoutList;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LayoutHandler extends SingletonFactory {
	public $layoutIDs = array();

	public function init() {
		$list = new LayoutList();
		$list->readObjects();
		$list = $list->getObjects();
		
		foreach ($list as $item) {
			$this->layoutIDs[] = $item->layoutID;
		}
	}

	public function getStylesheet($layoutID) {
		$filename = RELATIVE_CMS_DIR . 'style/layout-' . $layoutID . '.css';
		if (! file_exists($filename)) {
			LayoutCompiler::getInstance()->compile(new Layout($layoutID));
		}
		return '<link rel="stylesheet" type="text/css" href="' . $filename . '" />';
	}

	public function deleteStylesheet($layoutID) {
		$filename = RELATIVE_CMS_DIR . 'style/layout-' . $layoutID . '.css';
		if (file_exists($filename)) {
			LayoutCompiler::getInstance()->kill(new Layout($layoutID));
		}
	}
}
