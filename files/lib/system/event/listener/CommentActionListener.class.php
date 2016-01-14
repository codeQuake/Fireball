<?php
namespace cms\system\event\listener;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\listener\IParameterizedEventListener;
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
class CommentActionListener implements IParameterizedEventListener {
	const OBJECT_TYPE = 'de.codequake.cms.page.comment';

	/**
	 * event object
	 * @var	\wcf\data\comment\CommentAction
	 */
	protected $eventObj;

	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		$this->eventObj = $eventObj;

		if (method_exists($this, $this->eventObj->getActionName())) {
			call_user_func(array($this, $this->eventObj->getActionName()));
		}
	}

	/**
	 * Fires notification event to notify all subscribers when added a commit.
	 */
	protected function addComment() {
		$params = $this->eventObj->getParameters();
		$objectType = ObjectTypeCache::getInstance()->getObjectType($params['data']['objectTypeID']);
		$comment = $this->eventObj->createdComment;
		
		if ($comment !== null && $objectType->objectType == self::OBJECT_TYPE) {
			$notificationObjectType = UserNotificationHandler::getInstance()->getObjectTypeProcessor(self::OBJECT_TYPE);
			$notificationObject = new CommentUserNotificationObject($comment);
			
			UserObjectWatchHandler::getInstance()->updateObject('de.codequake.cms.page', $comment->objectID, 'comment', self::OBJECT_TYPE, $notificationObject);
		}
	}
}
