<?php
namespace cms\system\user\notification\event;

use cms\data\page\PageCache;
use wcf\data\comment\Comment;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * Notification event to notify the comment author about a new response.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {
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
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.title.stacked', [
				'count' => $count,
				'timesTriggered' => $this->notification->timesTriggered
			]);
		}
		
		return $this->getLanguage()->get('cms.page.commentResponse.notification.title');
	}
	
	/**
	 * @inheritDoc
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
			
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message.stacked', [
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message', [
			'page' => $page,
			'author' => $this->author
		]);
	}
	
	/**
	 * @inheritDoc
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
			
			return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail.stacked', [
				'page' => $page,
				'author' => $this->author,
				'authors' => array_values($authors),
				'count' => $count,
				'others' => $count - 1,
				'notificationType' => $notificationType,
				'guestTimesTriggered' => $this->notification->guestTimesTriggered
			]);
		}
		
		return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail', [
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
		$comment = new Comment($this->userNotificationObject->commentID);
		$page = PageCache::getInstance()->getPage($comment->objectID);
		
		return $page->getLink();
	}
}
