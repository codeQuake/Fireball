<?php
namespace cms\system\user\online\location;
use wcf\data\user\online\UserOnline;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;
use cms\data\news\News;

class NewsLocation implements IUserOnlineLocation{
    public function cache(UserOnline $user) {}
    
    public function get(UserOnline $user, $languageVariable = '') {
        $news = new News($user->objectID);
        if($news->newsID != 0){
            if($news->isVisible()){
                return WCF::getLanguage()->getDynamicVariable($languageVariable, array('news' => $news));
            }
        }
        return '';
    
    }
}