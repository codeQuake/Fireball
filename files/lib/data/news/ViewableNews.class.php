<?php
namespace cms\data\news;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\visitTracker\VisitTracker;

class ViewableNews extends DatabaseObjectDecorator{
    protected static $baseClass = 'cms\data\news\News';

    protected $effectiveVisitTime = null;
    
    
    public function getVisitTime() {
		if ($this->effectiveVisitTime === null) {
			if (WCF::getUser()->userID) {
				$this->effectiveVisitTime = max($this->visitTime, VisitTracker::getInstance()->getVisitTime('de.codequake.cms.news'));
			}
			else {
				$this->effectiveVisitTime = max(VisitTracker::getInstance()->getObjectVisitTime('de.codequake.cms.news', $this->newsID), VisitTracker::getInstance()->getVisitTime('de.codequake.cms.news'));
			}
			if ($this->effectiveVisitTime === null) {
				$this->effectiveVisitTime = 0;
			}
		}
		
		return $this->effectiveVisitTime;
	}
    
    public function isNew() {
		if ($this->lastChangeTime > $this->getVisitTime()) {
			return true;
		}
		
		return false;
	}
	
}