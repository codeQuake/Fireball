<?php
namespace cms\system\user\activity\event;

use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use cms\data\news\NewsList;

class NewsUserActivityEvent extends SingletonFactory implements IUserActivityEvent{
    public function prepare(array $events){
        $objectIDs = array();
        foreach ($events as $event) {
            $objectIDs[] = $event->objectID;
        }
        $list = new NewsList();
        $list->getConditionBuilder()->add("news.newsID IN (?)", array($objectIDs));
        $list->readObjects();
        $newss = $list->getObjects();
        
        foreach($events as $event){
            if(isset($newss[$event->objectID]))
            {
                $news = $newss[$event->objectID];
                $text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.news', array(
                    'news' => $news));
                $event->setTitle($text);
                $event->setDescription($news->getExcerpt());
                $event->setIsAccessible();
            }
            else {$event->setIsOrphaned();}
            
            
            
           
        }
        
    }

}