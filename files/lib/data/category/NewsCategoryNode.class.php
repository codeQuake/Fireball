<?php
namespace cms\data\category;
use wcf\data\category\CategoryNode;


class NewsCategoryNode extends CategoryNode{

    protected static $baseClass = 'cms\data\category\NewsCategory';
    protected $unreadNews = null;
    
    public function getUnreadNews(){
        if($this->unreadNews === null) $this->unreadNews = NewsCategoryCache::getInstance()->getUnreadNews($this->categoryID);
        return $this->unreadNews;
    }
}