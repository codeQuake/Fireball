<?php
namespace cms\system\user\activity\event;

use cms\data\news\NewsList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsUserActivityEvent extends SingletonFactory implements IUserActivityEvent {

	public function prepare(array $events) {
		$objectIDs = array();
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		$list = new NewsList();
		$list->getConditionBuilder()->add("news.newsID IN (?)", array(
			$objectIDs
		));
		$list->readObjects();
		$newss = $list->getObjects();
		
		foreach ($events as $event) {
			if (isset($newss[$event->objectID])) {
				$news = $newss[$event->objectID];
				$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.news', array(
					'news' => $news
				));
				$event->setTitle($text);
				$event->setDescription($news->getExcerpt());
				$event->setIsAccessible();
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
