<?php
namespace cms\acp\page;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use cms\system\counter\VisitCountHandler;

class DashboardPage extends AbstractPage{
    public $templateName = 'dashboard';
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.dashboard';
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign('visitors', VisitCountHandler::getInstance());
    }
}
