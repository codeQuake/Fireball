<?php
namespace cms\system\user\notification\event;
use cms\data\page\Page;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

class PageCommentUserNotificationEvent extends AbstractUserNotificationEvent {

	public function getTitle() {
		return $this->getLanguage()->get('cms.page.comment.notification.title');
	}
	
	public function getMessage() {
		$page = new Page($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.message', array(
			'page' => $page,
			'author' => $this->author
		));
	}

	public function getEmailMessage($notificationType = 'instant') {
		$page = new Page($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.page.comment.notification.mail', array(
			'page' => $page,
			'author' => $this->author
		));
	}

	public function getLink() {
		$page = new Page($this->userNotificationObject->objectID);
		
		return LinkHandler::getInstance()->getLink('Page', array(
			'application' => 'cms',
			'object' => $page
		), '#comments');
	}
}
