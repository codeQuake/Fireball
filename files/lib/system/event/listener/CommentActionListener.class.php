<?php
namespace cms\system\event\listener;

use wcf\system\event\IEventListener;
use wcf\system\user\notification\object\CommentUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\WCF;

/**
 * Fires notifications for new comments for a watched page.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CommentActionListener implements IEventListener {
	const OBJECT_TYPE = 'de.codequake.cms.page.comment';

	/**
	 * event object
	 * @var	\wcf\data\comment\CommentAction
	 */
	protected $eventObj;

	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$this->eventObj = $eventObj;

		if (method_exists($this, $this->eventObj->getActionName())) {
			call_user_func(array($this, $this->eventObj->getActionName()));
		}
	}

	/**
	 * Fires notification event to notify all subscribers when added a commit.
	 */
	protected function addComment() {
		// Fetch latest comment, it's the one we want to work with.
		// WCF 2.1 will provide direct access to the comment
		$sql = "SELECT		*
			FROM		wcf".WCF_N."_comment
			ORDER BY	commentID DESC";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute();
		$comment = $statement->fetchObject('wcf\data\comment\Comment');

		$notificationObjectType = UserNotificationHandler::getInstance()->getObjectTypeProcessor(self::OBJECT_TYPE);
		$notificationObject = new CommentUserNotificationObject($comment);

		UserObjectWatchHandler::getInstance()->updateObject('de.codequake.cms.page', $comment->objectID, 'comment', self::OBJECT_TYPE, $notificationObject);
	}
}
