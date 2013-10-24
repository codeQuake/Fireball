<?php
namespace cms\data\news;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\system\language\LanguageFactory;

class NewsAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\news\NewsEditor';
    protected $permissionsDelete = array('mod.cms.news.canModerateNews');
    
    
    public function create(){
        $data = $this->parameters['data'];
        if (LOG_IP_ADDRESS) {
            // add ip address
            if (!isset($data['ipAddress'])) {
                $data['ipAddress'] = WCF::getSession()->ipAddress;
            }
        }
        else {
            // do not track ip address
            if (isset($data['ipAddress'])) {
                unset($data['ipAddress']);
            }
        }
        
        $news = call_user_func(array($this->className,'create'), $data);
        $newsEditor = new NewsEditor($news);
		
		// handle categories
		$newsEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$newsEditor->setCategoryIDs($this->parameters['categoryIDs']);
        
        //langID != 0
        $languageID = (!isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];
        
        return $news;
        
    }
}