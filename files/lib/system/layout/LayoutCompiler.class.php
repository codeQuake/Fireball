<?php
namespace cms\system\layout;

use cms\data\layout\Layout;
use cms\data\stylesheet\LayoutStylesheetList;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\util\FileUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class LayoutCompiler extends SingletonFactory {
	protected $compiler = null;

	public function init() {
		require_once (WCF_DIR . 'lib/system/style/lessc.inc.php');
		$this->compiler = new \lessc();
		$this->compiler->setImportDir(array(
			WCF_DIR
		));
	}

	public function compile(Layout $layout) {
		// create sheet list
		$list = new LayoutStylesheetList($layout->layoutID);
		$list->readObjects();
		$list = $list->getObjects();
		// merge used sheets
		$less = '';
		foreach ($list as $sheet) {
			$less .= ' ' . $sheet->less;
		}
		$content = '';
		try {
			$content = $this->compiler->compile($less);
		}
		catch (\Exception $e) {
			throw new SystemException("Could not compile LESS: " . $e->getMessage(), 0, '', $e);
		}
		
		file_put_contents(CMS_DIR . 'style/layout-' . $layout->layoutID . '.css', $content);
		FileUtil::makeWritable(CMS_DIR . 'style/layout-' . $layout->layoutID . '.css');
	}

	public function kill(Layout $layout) {
		unlink(CMS_DIR . 'style/layout-' . $layout->layoutID . '.css');
	}
}
