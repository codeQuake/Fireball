<?php
namespace cms\system\user\notification\event;

use cms\data\page\PageCache;
use wcf\data\comment\Comment;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event to notify subscribers about new comments.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.title', array(
			'page' => PageCache::getInstance()->getPage($this->userNotificationObject->pageID)
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);

		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.message', array(
			'author' => $this->author,
			'page' => $page,
			'userNotificationObject' => $this->userNotificationObject
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);

		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.mail', array(
			'author' => $this->author,
			'page' => $page,
			'userNotificationObject' => $this->userNotificationObject
		));
	}

	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getLink()
	 */
	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);

		return $page->getLink();
	}
}
