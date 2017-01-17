<?php
namespace cms\system\user\activity\event;

use cms\data\page\PageCache;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\comment\CommentList;
use wcf\data\user\UserList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for page comment responses.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @see	\wcf\system\user\activity\event\IUserActivityEvent::prepare()
	 */
	public function prepare(array $events) {
		$commentsIDs = $responseIDs = $userIDs = [];

		foreach ($events as $event) {
			$responseIDs[] = $event->objectID;
		}

		$responseList = new CommentResponseList();
		$responseList->setObjectIDs($responseIDs);
		$responseList->readObjects();
		$responses = $responseList->getObjects();

		foreach ($responses as $response) {
			$commentIDs[] = $response->commentID;
		}

		$commentList = new CommentList();
		$commentList->setObjectIDs($commentIDs);
		$commentList->readObjects();
		$comments = $commentList->getObjects();

		foreach ($comments as $comment) {
			if (!in_array($comment->userID, $userIDs)) {
				$userIDs[] = $comment->userID;
			}
		}

		$userList = new UserList();
		$userList->setObjectIDs($userIDs);
		$userList->readObjects();
		$users = $userList->getObjects();

		foreach ($events as $event) {
			if (isset($responses[$event->objectID])) {
				$response = $responses[$event->objectID];
				$comment = $comments[$response->commentID];
				$page = PageCache::getInstance()->getPage($comment->objectID);

				if ($page !== null && isset($users[$comment->userID])) {
					if (!$page->canRead()) {
						continue;
					}

					$event->setIsAccessible();

					$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.pageCommentResponse', [
						'author' => $users[$comment->userID],
						'page' => $page
					]);
					$event->setTitle($text);
					$event->setDescription($response->getFormattedMessage());

					continue;
				}
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
