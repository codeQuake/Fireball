<?php
namespace  cms\system\user\notification\event;
use cms\data\news\News;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

class NewsCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {

	public function getTitle() {
		return $this->getLanguage()->get('cms.news.commentResponse.notification.title');
	}

	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.commentResponse.notification.message', array(
			'news' => $news,
			'author' => $this->author
		));
	}
	
	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.commentResponse.notification.mail', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	public function getLink() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $news
		), '#comments');
	}
}
