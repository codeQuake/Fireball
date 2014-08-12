<?php
namespace cms\system\layout;

use cms\data\page\Page;
use cms\data\stylesheet\PageStylesheetList;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
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

	public function compile(Page $page) {
		// create sheet list
		$list = new PageStylesheetList($page->pageID);
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

		file_put_contents(CMS_DIR . 'style/layout-' . $page->pageID . '.css', $content);
		FileUtil::makeWritable(CMS_DIR . 'style/layout-' . $page->pageID . '.css');
	}

	public function kill(Page $page) {
		unlink(CMS_DIR . 'style/layout-' . $page->pageID . '.css');
	}
}
