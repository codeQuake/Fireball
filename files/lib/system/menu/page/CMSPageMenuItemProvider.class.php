<?php
namespace cms\system\menu\page;
use wcf\system\menu\page\DefaultPageMenuItemProvider;
use cms\data\page\Page;

class CMSPageMenuItemProvider extends DefaultPageMenuItemProvider{
    
    protected $page = null;
    
    public function getPage(){
        $tmp = explode("=", $this->getDecoratedObject()->menuItemLink);
        $this->page =  new Page(intval($tmp[1]));
    }

	public function getLink(){
        $this->getPage();
        if($this->page === null || $this->page->pageID == 0) return parent::getLink();
        return $this->page->getLink();
    }   
}