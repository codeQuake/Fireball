<?php
namespace cms\data\stylesheet;

use cms\data\page\PageCache;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageStylesheetList extends ViewableStylesheetList {

	public $pageID = 0;

	public function __construct($pageID) {
		$this->pageID = $pageID;
		$page = PageCache::getInstance()->getPage($pageID);
		$data = @unserialize($page->stylesheets);
		parent::__construct();
		$this->getConditionBuilder()->add('stylesheet.sheetID IN (?)', array(
			$data
		));
	}
}
