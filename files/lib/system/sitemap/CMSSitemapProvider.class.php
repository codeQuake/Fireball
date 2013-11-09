<?php
namespace cms\system\sitemap;
use cms\data\page\PageList;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

class CMSSitemapProvider implements ISitemapProvider{
    
    
    public function getTemplate(){
        $list = new PageList();
        $list->getConditionBuilder()->add('page.parentID = ?', array(0));
        $list->readObjects();
        $list = $list->getObjects();
        
        WCF::getTPL()->assign(array('pageList' => $list));
        
        return WCF::getTPL()->fetch('cmsSitemap','cms');
    }
}