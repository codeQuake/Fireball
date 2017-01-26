<?php
namespace cms\system\user\activity\event;

use cms\data\page\PageCache;
use wcf\data\comment\CommentList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for page comments.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @inheritDoc
	 */
	public function prepare(array $events) {
		$commentIDs = [];
		foreach ($events as $event) {
			$commentIDs[] = $event->objectID;
		}

		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add('comment.commentID IN (?)', [$commentIDs]);
		$commentList->readObjects();
		$comments = $commentList->getObjects();

		foreach ($events as $event) {
			if (isset($comments[$event->objectID])) {
				$comment = $comments[$event->objectID];
				$page = PageCache::getInstance()->getPage($comment->objectID);

				if ($page !== null) {
					if (!$page->canRead()) {
						continue;
					}

					$event->setIsAccessible();

					$text = WCF::getLanguage()->getDynamicVariable('wcf.user.profile.recentActivity.pageComment', [
						'page' => $page
					]);
					$event->setTitle($text);
					$event->setDescription($comment->getFormattedMessage());

					continue;
				}
			}

			$event->setIsOrphaned();
		}
	}
}
