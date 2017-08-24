<?php
namespace cms\system\user\notification\event;

use cms\data\page\PageCache;
use wcf\data\comment\Comment;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event to notify subscribers about new comments.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritDoc
	 */
	protected $stackable = true;
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		$count = count($this->getAuthors());
		if ($count > 1) {
			return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.title.stacked', [
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			]);
		}
		
		return $this->getLanguage()->get('cms.page.comment.notification.title');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);
		
		$authors = $this->getAuthors();
		if (count($authors) > 1) {
			if (isset($authors[0])) {
				unset($authors[0]);
			}
			$count = count($authors);
			
			return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.message.stacked', [
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.message', [
				'page' => $page,
				'author' => $this->author
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);
		
		$authors = $this->getAuthors();
		if (count($authors) > 1) {
			if (isset($authors[0])) {
				unset($authors[0]);
			}
			$count = count($authors);
			
			return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.mail.stacked', [
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'notificationType' => $notificationType,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.mail', [
			'page' => $page,
			'comment' => $comment,
			'author' => $this->author,
			'notificationType' => $notificationType
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		$page = PageCache::getInstance()->getPage($this->userNotificationObject->objectID);
		
		return $page->getLink();
	}
}
