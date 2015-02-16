<?php
namespace cms\system\user\notification\event;

use cms\data\page\PageCache;
use wcf\data\comment\Comment;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event to notify the comment author about a new response.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->get('cms.page.commentResponse.notification.title');
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);

		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message', array(
			'page' => PageCache::getInstance()->getPage($comment->objectID),
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);

		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail', array(
			'page' => PageCache::getInstance()->getPage($comment->objectID),
			'author' => $this->author
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($comment->objectID);

		return $page->getLink();
	}
}
