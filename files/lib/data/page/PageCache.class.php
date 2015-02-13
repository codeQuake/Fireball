<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the page cache.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCache extends SingletonFactory {
	/**
	 * alias to page assignments
	 * @var	array<integer>
	 */
	protected $aliasToPage = array();

	/**
	 * cached pages
	 * @var	array<\cms\data\page\Page>
	 */
	protected $pages = array();

	/**
	 * cached page structure
	 * @var	array<array<integer>>
	 */
	protected $structure = array();

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->aliasToPage = PageCacheBuilder::getInstance()->getData(array(), 'aliasToPage');
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		$this->stylesheetsToPage = PageCacheBuilder::getInstance()->getData(array(), 'stylesheetsToPage');
		$this->structure = PageCacheBuilder::getInstance()->getData(array(), 'structure');
	}

	/**
	 * Returns the page id of the page with the given alias.
	 * 
	 * @param	string		$alias
	 * @return	integer
	 */
	public function getIDByAlias($alias) {
		if (isset($this->aliasToPage[$alias])) return $this->aliasToPage[$alias];
		return 0;
	}

	/**
	 * Returns the page with the given id from cache.
	 * 
	 * @param	integer		$pageID
	 * @return	\cms\data\page\Page
	 */
	public function getPage($pageID) {
		if (isset($this->pages[$pageID])) {
			return $this->pages[$pageID];
		}

		return null;
	}

	public function getHomePage() {
		foreach ($this->pages as $page) {
			if ($page->isHome) {
				return $page;
			}
		}

		return null;
	}

	/**
	 * Returns the ids of the child pages of the given page.
	 * 
	 * @param	integer		$pageID
	 * @return	array<integer>
	 */
	public function getChildIDs($pageID) {
		if (isset($this->structure[$pageID])) {
			return $this->structure[$pageID];
		}

		return array();
	}

	/**
	 * Returns the stylesheet ids of the given page.
	 * 
	 * @param	integer		$pageID
	 * @return	array<integer>
	 */
	public function getStylesheetIDs($pageID) {
		if (isset($this->stylesheetsToPage[$pageID])) {
			return $this->stylesheetsToPage[$pageID];
		}

		return array();
	}
}
