<?php
namespace cms\acp\page;
use cms\data\page\PageList;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use cms\system\counter\VisitCountHandler;
use wcf\data\user\online\UsersOnlineList;

class StatsPage extends AbstractPage{
    public $templateName = 'stats';
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.statistics';
    public $startDate = 0;
    public $endDate = 0;
    public $visits = array();
    public $browsers = array();
    public $colors = array('#015294', '#F7464A', '#E2EAE9', '#D4CCC5', '#949FB1', '#4D5360', '#F38630', '#f0f0f0', '#1f1f1'); 
    public $pages = null;
    
    public function readParamters(){
        parent::readParameters();
    }
    
    public function readData(){
        parent::readData();
        //set default dates
        if($this->startDate == 0) $this->startDate = TIME_NOW - 604800;
        if($this->endDate == 0)$this->endDate = TIME_NOW;
        
        //get stats
        $this->visits = VisitCountHandler::getInstance()->getVisitors($this->startDate, $this->endDate);
        
        $this->browsers = VisitCountHandler::getInstance()->getBrowsers($this->startDate, $this->endDate);
                
        //read pages
        $list = new PageList();
        $list->sqlOrderBy = 'page.clicks DESC';
        $list->sqlLimit = '8';
        $list->readObjects();
        $this->pages = $list->getObjects();
        
        //user online list
        $this->usersOnlineList = new UsersOnlineList();
		$this->usersOnlineList->readStats();
		$this->usersOnlineList->readObjects();
            
    }
    
    
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('visits' => $this->visits,
                                    'browsers' => $this->browsers,
                                    'objects' => $this->usersOnlineList,
                                    'colors' => $this->colors,
                                    'pages' => $this->pages));
    }
}
