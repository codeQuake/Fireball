<?php
namespace cms\data\news;
use wcf\system\WCF;
use wcf\system\visitTracker\VisitTracker;
use cms\data\news\NewsList;

class ViewableNewsList extends NewsList{
    public $decoratorClassName = 'cms\data\news\ViewableNews';
    
    public function __construct(){
        parent::__construct();
            if (WCF::getUser()->userID != 0) {
			        // last visit time
			        if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
			        $this->sqlSelects .= 'tracked_visit.visitTime';
			        $this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.codequake.cms.news')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")";
		        }
    }
}