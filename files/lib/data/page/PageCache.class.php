<?php
namespace cms\data\page;

use cms\data\page\AccessiblePageNodeTree;
use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the page cache.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCache extends SingletonFactory {
	/**
	 * alias to page assignments
	 * @var	array<integer>
	 */
	protected $aliasToPage = [];

	/**
	 * cached pages
	 * @var	array<\cms\data\page\Page>
	 */
	protected $pages = [];

	/**
	 * cached page structure
	 * @var	array<array<integer>>
	 */
	protected $structure = [];

	/**
	 * cached menu node tree (max depth = 1)
	 * @var	\cms\data\page\AccessiblePageNodeTree
	 */
	protected $menuNodeTree = null;

	/**
	 * cached menu node list (max depth = 1)
	 * @var	\cms\data\page\AccessiblePageNodeTree
	 */
	protected $menuNodeList = null;

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->aliasToPage = PageCacheBuilder::getInstance()->getData([], 'aliasToPage');
		$this->pages = PageCacheBuilder::getInstance()->getData([], 'pages');
		$this->stylesheetsToPage = PageCacheBuilder::getInstance()->getData([], 'stylesheetsToPage');
		$this->structure = PageCacheBuilder::getInstance()->getData([], 'structure');
		
		$this->menuNodeTree = new AccessiblePageNodeTree();
		$this->menuNodeTree->setMaxDepth(1);
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

	/**
	 * Returns the pages from cache.
	 *
	 * @return	\cms\data\page\Page[]
	 */
	public function getPages() {
		return $this->pages;
	}

	public function getMenuNodeTree() {
		if ($this->menuNodeTree === null) {
			$this->menuNodeTree = new AccessiblePageNodeTree();
			$this->menuNodeTree->setMaxDepth(1);
		}
		
		return $this->menuNodeTree;
	}

	/**
	 * Returns a node tree with max depth 1
	 * can be used for an automatic page menu or sitemap
	 * 
	 * @return \cms\data\page\AccessiblePageNodeTree
	 */
	public function getMenuNodeList() {
		if ($this->menuNodeList === null) {
			$nodeTree = $this->getMenuNodeTree();
			$this->menuNodeList = $nodeTree->getIterator();
			$this->menuNodeList->setMaxDepth(1);
		}
		
		return $this->menuNodeList;
	}

	/**
	 * Returns a node list with max depth 1
	 * can be used for an automatic page menu or sitemap
	 *
	 * @return \cms\data\page\AccessiblePageNodeTree
	 */
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

		return [];
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

		return [];
	}
}
