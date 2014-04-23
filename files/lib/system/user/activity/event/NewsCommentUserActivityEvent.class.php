<?php
namespace cms\system\user\activity\event;

use cms\data\news\NewsList;
use wcf\data\comment\CommentList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {

	public function prepare(array $events) {
		$objectIDs = array();
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// comments
		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array(
			$objectIDs
		));
		$commentList->readObjects();
		$comments = $commentList->getObjects();
		
		// get news
		$newsIDs = array();
		foreach ($comments as $comment) {
			$newsIDs[] = $comment->objectID;
		}
		
		$list = new NewsList();
		$list->getConditionBuilder()->add("news.newsID IN (?)", array(
			$newsIDs
		));
		$list->readObjects();
		$newss = $list->getObjects();
		
		foreach ($events as $event) {
			if (isset($comments[$event->objectID])) {
				$comment = $comments[$event->objectID];
				if (isset($newss[$comment->objectID])) {
					$news = $newss[$comment->objectID];
					$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.newsComment', array(
						'news' => $news
					));
					$event->setTitle($text);
					$event->setDescription($comment->getFormattedMessage());
					$event->setIsAccessible();
				}
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
