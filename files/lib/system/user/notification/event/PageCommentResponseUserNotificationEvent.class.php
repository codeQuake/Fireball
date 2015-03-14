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
	 * @see	\wcf\system\user\notification\event\AbstractUserNotificationEvent::$stackable
	 */
	protected $stackable = true;
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getTitle()
	 */
	public function getTitle() {
		$count = count($this->getAuthors());
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.title.stacked', array(
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			));
		}
		
		return $this->getLanguage()->get('cms.page.commentResponse.notification.title');
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getMessage()
	 */
	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($comment->objectID);
		
		$authors = $this->getAuthors();
		if (count($authors) > 1) {
			if (isset($authors[0])) {
				unset($authors[0]);
			}
			$count = count($authors);
			
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message.stacked', array(
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			));
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message', array(
			'page' => $page,
			'author' => $this->author
		));
	}
	
	/**
	 * @see	\wcf\system\user\notification\event\IUserNotificationEvent::getEmailMessage()
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($comment->objectID);
		
		$authors = $this->getAuthors();
		if (count($authors) > 1) {
			if (isset($authors[0])) {
				unset($authors[0]);
			}
			$count = count($authors);
			
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail.stacked', array(
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'notificationType' => $notificationType,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			));
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail', array(
			'page' => $page,
			'comment' => $comment,
			'author' => $this->author,
			'notificationType' => $notificationType
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
