<?php
namespace cms\data\page;

use wcf\data\DatabaseObjectDecorator;

class ViewablePage extends DatabaseObjectDecorator implements \Countable \Iterator{
    protected static $baseClass = 'cms\data\page\Page';
    protected $index = 0;
    protected $objects = array();
    
    public function addChild(Page $page) {
		if ($page->parentMenuItem == $this->page) {
			$this->objects[] = $page;
		}
	}
    
    public function count() {
		return count($this->objects);
	}
    
    public function current() {
		return $this->objects[$this->index];
	}
    
    public function key() {
		return $this->index;
	}
	

	public function next() {
		++$this->index;
	}
	

	public function rewind() {
		$this->index = 0;
	}
	

	public function valid() {
		return isset($this->objects[$this->index]);
	}
}
