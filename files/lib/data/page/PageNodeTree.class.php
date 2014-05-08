<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageNodeTree implements \IteratorAggregate {

	protected $nodeClassName = 'cms\data\page\PageNode';

	protected $parentID = null;

	protected $parentNode = null;

	public function __construct($parentID = null) {
		$this->parentID = $parentID;
	}

	public function buildTree() {
		$this->parentNode = $this->getNode($this->parentID);
		$this->buildTreeLevel($this->parentNode);
	}

	public function buildTreeLevel(PageNode $pageNode) {
		foreach ($this->getChildren($pageNode) as $child) {
			$childNode = $this->getNode($child->pageID);
			if ($this->isIncluded($childNode)) {
				$pageNode->addChild($childNode);
				$this->buildTreeLevel($childNode);
			}
		}
	}

	protected function getPage($pageID) {
		return PageCache::getInstance()->getPage($pageID);
	}

	protected function getChildren(PageNode $parentNode) {
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');

		$children = array();
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
		if (! $pageID) {
			$page = new Page(0);
		} else {
			$page = $this->getPage($pageID);
		}

		return new $this->nodeClassName($page);
	}

	protected function isIncluded(PageNode $pageNode) {
		return true;
	}
}
