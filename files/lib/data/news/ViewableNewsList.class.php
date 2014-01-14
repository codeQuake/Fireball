<?php
namespace cms\data\news;
use wcf\system\WCF;
use wcf\system\visitTracker\VisitTracker;
use cms\data\news\NewsList;
use wcf\system\like\LikeHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class ViewableNewsList extends NewsList{
    public $decoratorClassName = 'cms\data\news\ViewableNews';
    public $sqlOrderBy = 'news.time DESC';
    public function __construct(){
        parent::__construct();
            if (WCF::getUser()->userID != 0) {
			        // last visit time
			        if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
			        $this->sqlSelects .= 'tracked_visit.visitTime';
			        $this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_tracked_visit tracked_visit ON (tracked_visit.objectTypeID = ".VisitTracker::getInstance()->getObjectTypeID('de.codequake.cms.news')." AND tracked_visit.objectID = news.newsID AND tracked_visit.userID = ".WCF::getUser()->userID.")";
		        }
            // get like status
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "like_object.likes, like_object.dislikes";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_like_object like_object ON (like_object.objectTypeID = ".LikeHandler::getInstance()->getObjectType('de.codequake.cms.likeableNews')->objectTypeID." AND like_object.objectID = news.newsID)";
    }
}