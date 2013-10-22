<?php
namespace cms\page;
use cms\data\category\NewsCategoryNodeTree;
use wcf\page\AbstractPage;

class NewsCategoryListPage extends AbstractPage{

    public $activeMenuItem = 'cms.page.news';
    public $enableTracking = true;
    
    public $categoryList = null;
    
    
    public function readData(){
        parent::readData();
        $categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news');
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);
        
        if (PageMenu::getInstance()->getLandingPage()->menuItem == 'cms.page.news') {
            WCF::getBreadcrumbs()->remove(0);

            MetaTagHandler::getInstance()->addTag('og:url', 'og:url', LinkHandler::getInstance()->getLink('NewsList', array('application' => 'cms')), true);
            MetaTagHandler::getInstance()->addTag('og:type', 'og:type', 'website', true);
            MetaTagHandler::getInstance()->addTag('og:title', 'og:title', WCF::getLanguage()->get(PAGE_TITLE), true);
            MetaTagHandler::getInstance()->addTag('og:description', 'og:description', WCF::getLanguage()->get(PAGE_DESCRIPTION), true);
        }
    }
    
    public function assignVariables(){
        parent::assignVariables();

        WCF::getTPL()->assign(array(
            'categoryList' => $this->categoryList,
            'allowSpidersToIndexThisPage' => true
            ));
    }
}