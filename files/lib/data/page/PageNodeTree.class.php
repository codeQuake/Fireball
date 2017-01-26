<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;

/**
 * Generates a tree of all pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageNodeTree implements \IteratorAggregate {

	protected $nodeClassName = PageNode::class;

	protected $parentID = null;

	protected $parentNode = null;
	
	protected $maxDepth = -1;

	public function __construct($parentID = null) {
		$this->parentID = $parentID;
	}

	public function buildTree() {
		$this->parentNode = $this->getNode($this->parentID);
		$this->buildTreeLevel($this->parentNode, $this->maxDepth);
	}

	public function buildTreeLevel(PageNode $pageNode, $depth = 0) {
		if ($this->maxDepth != -1 && $depth < 0) {
			return;
		}
		
		foreach ($this->getChildren($pageNode) as $child) {
			$childNode = $this->getNode($child->pageID);
			if ($this->isIncluded($childNode)) {
				$pageNode->addChild($childNode);
				$this->buildTreeLevel($childNode, $depth - 1);
			}
		}
	}

	protected function getPage($pageID) {
		return PageCache::getInstance()->getPage($pageID);
	}

	protected function getChildren(PageNode $parentNode) {
		$pages = PageCacheBuilder::getInstance()->getData([], 'pages');

		$children = [];
		foreach ($pages as $page) {
			if ($page->parentID == $parentNode->pageID) {
				$children[$page->pageID] = $page;
			}
		}
		return $children;
	}

	public function getIterator() {
		if ($this->parentNode === null) {
			$this->buildTree();
		}

		return new \RecursiveIteratorIterator($this->parentNode, \RecursiveIteratorIterator::SELF_FIRST);
	}

	protected function getNode($pageID) {
		if (!$pageID) {
			// @todo: This needs to be changed. It creates a
			// pointless database query to fetch an (of course) not
			// existing page with the id '0'
			$page = new Page(null, ['pageID' => 0]);
		}
		else {
			$page = $this->getPage($pageID);
		}

		return new $this->nodeClassName($page);
	}

	protected function isIncluded(PageNode $pageNode) {
		return true;
	}
	

	public function setMaxDepth($maxDepth) {
		$this->maxDepth = $maxDepth;
	}
}
