<?php
namespace cms\data\page;

use wcf\data\DatabaseObjectDecorator;

class PageNode extends DatabaseObjectDecorator implements \RecursiveIterator, \Countable {

    protected $children = array();
    protected $index = 0;
    protected $parentNode = null;
    protected static $baseClass = 'cms\data\page\Page';
    
    public function addChild(PageNode $pageNode) {
		$pageNode->setParentNode($this);
		
		$this->children[] = $pageNode;
	}
    
    public function setParentNode(PageNode $parentNode) {
		$this->parentNode = $parentNode;
	}
    
    public function isLastSibling() {
		foreach ($this->parentNode as $key => $child) {
			if ($child === $this) {
				if ($key == count($this->parentNode) - 1) return true;
				return false;
			}
		}
	}
    
    public function getOpenParentNodes() {
		$element = $this;
		$i = 0;
	
		while ($element->parentNode->parentNode != null && $element->isLastSibling()) {
			$i++;
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
		$this->index++;
	}
    
    public function rewind() {
		$this->index = 0;
	}
    
    public function valid() {
		return isset($this->children[$this->index]);
	}
}