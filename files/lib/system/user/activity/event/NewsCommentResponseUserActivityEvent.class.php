<?php
namespace cms\system\user\activity\event;

use cms\data\news\NewsList;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\comment\CommentList;
use wcf\data\user\User;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {

	public function prepare(array $events) {
		$objectIDs = array();
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// comments responses
		$responseList = new CommentResponseList();
		$responseList->getConditionBuilder()->add("comment_response.responseID IN (?)", array(
			$objectIDs
		));
		$responseList->readObjects();
		$responses = $responseList->getObjects();
		
		// comments
		$commentIDs = array();
		foreach ($responses as $response) {
			$commentIDs[] = $response->commentID;
		}
		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array(
			$commentIDs
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
			if (isset($responses[$event->objectID])) {
				$response = $responses[$event->objectID];
				if (isset($comments[$response->commentID])) {
					$comment = $comments[$response->commentID];
					if (isset($newss[$comment->objectID])) {
						$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.newsCommentResponse', array(
							'author' => new User($comment->userID),
							'news' => $newss[$comment->objectID]
						));
						$event->setTitle($text);
						$event->setDescription($response->getFormattedMessage());
						$event->setIsAccessible();
					}
				}
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
