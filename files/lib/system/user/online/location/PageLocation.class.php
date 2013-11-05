<?php
namespace cms\system\user\online\location;
use wcf\data\user\online\UserOnline;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;
use cms\data\page\Page;

class PageLocation implements IUserOnlineLocation{
    public function cache(UserOnline $user) {}
    
    public function get(UserOnline $user, $languageVariable = '') {
        $page = new Page($user->objectID);
        if($page->pageID != 0){
                return WCF::getLanguage()->getDynamicVariable($languageVariable, array('page' => $page));
        }
        return '';
    
    }
}