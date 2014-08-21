<?php
namespace cms\data\stylesheet;

use cms\data\page\PageCache;

/**
 * Represents a list of stylesheets that are assigned to a specific page.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageStylesheetList extends ViewableStylesheetList {
	/**
	 * page id
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * Creates a new list of stylesheets that are assigned to a specific
	 * page.
	 * 
	 * @param	integer		$pageID
	 */
	public function __construct($pageID) {
		parent::__construct();
		$this->pageID = $pageID;

		$page = PageCache::getInstance()->getPage($this->pageID);
		$data = @unserialize($page->stylesheets);

		$this->getConditionBuilder()->add('stylesheet.sheetID IN (?)', array(
			$data
		));
	}
}
