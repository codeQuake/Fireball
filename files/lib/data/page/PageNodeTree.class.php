<?php
namespace cms\data\page;

class PageNodeTree implements \IteratorAggregate {
    protected $nodeClassName = 'cms\data\page\PageNode';
    protected $parentPageID = 0;
    protected $parentNode = null;
    
    public function __construct($parentPageID = 0){
        $this->parentPageID = $parentPageID;
    }
    
    protected function buildTree() {
        $this->parentNode = $this->getNode($this->parentPageID);
        $this->buildTreeLevel($this->parentNode);
    }
    protected function buildTreeLevel(PageNode $parentNode) {
		foreach ($this->getChildCategories($parentNode) as $child) {
			$childNode = $this->getNode($child->pageID);
				$parentNode->addChild($childNode);
				
				// build next level
				$this->buildTreeLevel($childNode);
		}
	}
    
    protected function getNode($pageID) {
        $page = new Page($pageID);
        return new $this->nodeClassName($page);
    }
    
    protected function getChildCategories(PageNode $parentNode) {
        $pages = array();
        $list = new PageList();
        $list->readObjects();
        $list = $list->getObjects();
        foreach ($list as $page) {
			if ($page->parentID == $parentNode->pageID) {
				$pages[$page->pageID] = $page;
			}
		}
		
		return $pages;
    }
    
    public function getIterator() {
		if ($this->parentNode === null) {
			$this->buildTree();
		}
		
		return new \RecursiveIteratorIterator($this->parentNode, \RecursiveIteratorIterator::SELF_FIRST);
	}
    
    
}
