<?php
namespace cms\data\content;

use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a content node.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentNode extends DatabaseObjectDecorator implements \Countable, \RecursiveIterator {

	public $children = array();

	public $index = 0;

	public $parentNode = null;

	protected static $baseClass = 'cms\data\content\Content';

	/**
	 * @param ContentNode $contentNode
	 */
	public function addChild(ContentNode $contentNode) {
		$contentNode->setParentNode($this);
		$this->children[] = $contentNode;
	}

	public function setParentNode(ContentNode $contentNode) {
		$this->parentNode = $contentNode;
	}

	public function isLastSibling() {
		foreach ($this->parentNode as $key => $value) {
			if ($value == $this) {
				if ($key == count($this->parentNode) - 1) return true;
				return false;
			}
		}
	}

	public function getOpenParentNodes() {
		$element = $this;
		$i = 0;
		
		while ($element->parentNode->parentNode != null && $element->isLastSibling()) {
			$i ++;
			$element = $element->parentNode;
		}
		
		return $i;
	}

	public function count() {
		return count($this->children);
	}

	public function current() {
		return $this->children[$this->index];
	}

	public function getChildren() {
		return $this->children[$this->index];
	}

	public function hasChildren() {
		return !empty($this->children);
	}

	public function key() {
		return $this->index;
	}

	public function next() {
		$this->index ++;
	}

	public function rewind() {
		$this->index = 0;
	}

	public function valid() {
		return isset($this->children[$this->index]);
	}
}
