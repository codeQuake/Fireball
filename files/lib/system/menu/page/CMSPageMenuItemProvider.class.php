<?php
namespace cms\system\menu\page;
use wcf\system\menu\page\DefaultPageMenuItemProvider;

class CMSPageMenuItemProvider extends DefaultPageMenuItemProvider{
    
    protected $page = null;

	public function getLink(){
        if($this->page === null) return parent::getLink();
        $this->page->getLink();
    }   
}