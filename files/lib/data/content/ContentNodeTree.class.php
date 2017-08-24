<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;

/**
 * Builds the content tree
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentNodeTree implements \IteratorAggregate {
	protected $nodeClassName = ContentNode::class;

	/**
	 * id of the parent's content
	 * @var null|integer
	 */
	protected $parentID = null;

	/**
	 * @var null|ContentNode
	 */
	protected $parentNode = null;

	/**
	 * @var integer
	 */
	protected $pageID = 0;

	/**
	 * @var boolean
	 */
	protected $isACP = 0;

	public function __construct($parentID = null, $pageID = 0, $isACP = 0) {
		$this->parentID = $parentID;
		$this->pageID = $pageID;
		$this->isACP = $isACP;
	}

	public function buildTree() {
		$this->parentNode = $this->getNode($this->parentID);
		$this->buildTreeLevel($this->parentNode);
	}

	public function buildTreeLevel(ContentNode $contentNode) {
		foreach ($this->getChildren($contentNode) as $child) {
			$childNode = $this->getNode($child->contentID);
			if ($this->isIncluded($childNode)) {
				$contentNode->addChild($childNode);
				$this->buildTreeLevel($childNode);
			}
		}
	}

	protected function getContent($contentID) {
		return ContentCache::getInstance()->getContent($contentID);
	}

	protected function getChildren(ContentNode $parentNode) {
		$contents = ContentCacheBuilder::getInstance()->getData([], 'contents');

		$children = [];
		foreach ($contents as $content) {
			if ($content->parentID == $parentNode->contentID) {
				$children[$content->contentID] = $content;
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

	protected function getNode($contentID) {
		if (!$contentID) {
			$content = new Content(null, ['contentID' => 0]);
		}
		else {
			$content = $this->getContent($contentID);
		}

		return new $this->nodeClassName($content);
	}

	protected function isIncluded(ContentNode $contentNode) {
		if ($this->pageID != 0) {
			if ($contentNode->pageID != $this->pageID) return false;
		}
		if ($this->isACP) return true;
		
		return $contentNode->canRead();
	}
}

