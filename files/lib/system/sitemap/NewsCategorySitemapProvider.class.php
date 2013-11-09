<?php
namespace cms\system\sitemap;
use cms\data\category\NewsCategoryNodeTree;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

class NewsCategorySitemapProvider implements ISitemapProvider{
    
    public $objectTypeName = 'de.codequake.cms.category.news';
    
    public function getTemplate(){
        $nodeTree = new NewsCategoryNodeTree($this->objectTypeName);
        $nodeList = $nodeTree->getIterator();
        
        WCF::getTPL()->assign(array('nodeList' => $nodeList));
        
        return WCF::getTPL()->fetch('newsSitemap','cms');
    }
}