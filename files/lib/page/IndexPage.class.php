<?php
namespace cms\page;

use wcf\page\AbstractPage;
use wcf\system\WCF;

class IndexPage extends AbstractPage{
    
    public $layout = '';

    public function readData(){
        
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('allowSpidersToIndexThisPage'   =>  true,
                                    'layout' => $this->layout));
    }
}