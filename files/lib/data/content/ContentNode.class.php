<?php
namespace cms\data\content;

use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a content node.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @mixin Content
 * @method Content getDecoratedObject()
 */
class ContentNode extends DatabaseObjectDecorator implements \Countable, \RecursiveIterator {
	/**
	 * @var ContentNode[]
	 */
	public $children = [];
	
	/**
	 * @var integer
	 */
	public $index = 0;
	
	/**
	 * @var ContentNode|null
	 */
	public $parentNode = null;
	
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = Content::class;
	
	/**
	 * Adds a child as child node of the current node
	 * @param \cms\data\content\ContentNode $contentNode
	 */
	public function addChild(ContentNode $contentNode) {
		$contentNode->setParentNode($this);
		$this->children[] = $contentNode;
	}
	
	/**
	 * Sets the current node's parent node
	 * @param \cms\data\content\ContentNode $contentNode
	 */
	public function setParentNode(ContentNode $contentNode) {
		$this->parentNode = $contentNode;
	}
	
	/**
	 * Returns true if the current node is the last one
	 * @return boolean
	 */
	public function isLastSibling() {
		foreach ($this->parentNode as $key => $value) {
			if ($value == $this) {
				if ($key == count($this->parentNode) - 1) return true;
				return false;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the number of open parent nodes
	 * @return integer
	 */
	public function getOpenParentNodes() {
		$element = $this;
		$i = 0;
		
		while ($element->parentNode->parentNode != null && $element->isLastSibling()) {
			$i ++;
			$element = $element->parentNode;
		}
		
		return $i;
	}
	
	/**
	 * @inheritDoc
	 */
	public function count() {
		return count($this->children);
	}
	
	/**
	 * @inheritDoc
	 */
	public function current() {
		return $this->children[$this->index];
	}
	
	/**
	 * @inheritDoc
	 */
	public function getChildren() {
		return $this->children[$this->index];
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasChildren() {
		return !empty($this->children);
	}
	
	/**
	 * @inheritDoc
	 */
	public function key() {
		return $this->index;
	}
	
	/**
	 * @inheritDoc
	 */
	public function next() {
		$this->index ++;
	}
	
	/**
	 * @inheritDoc
	 */
	public function rewind() {
		$this->index = 0;
	}
	
	/**
	 * @inheritDoc
	 */
	public function valid() {
		return isset($this->children[$this->index]);
	}
}
