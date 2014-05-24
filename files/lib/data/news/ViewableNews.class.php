<?php
namespace cms\data\news;

use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableNews extends DatabaseObjectDecorator {
	protected static $baseClass = 'cms\data\news\News';
	protected $effectiveVisitTime = null;

	public $userProfile = null;

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

	public static function getNews($newsID) {
		$list = new ViewableNewsList();
		$list->setObjectIDs(array(
			$newsID
		));
		$list->readObjects();

		return $list->search($newsID);
	}

	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User($this->getDecoratedObject()->userID));
		}

		return $this->userProfile;
	}
}
