<?php
namespace cms\system\user\notification\event;

use cms\data\news\News;
use wcf\data\comment\Comment;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentResponseOwnerUserNotificationEvent extends AbstractUserNotificationEvent {

	public function getTitle() {
		return $this->getLanguage()->get('cms.news.commentResponseOwner.notification.title');
	}

	public function getMessage() {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		$commentAuthor = new User($comment->userID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.commentResponseOwner.notification.message', array(
			'news' => $news,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
		));
	}

	public function getEmailMessage($notificationType = 'instant') {
		$comment = new Comment($this->userNotificationObject->commentID);
		$news = new News($comment->objectID);
		$commentAuthor = new User($comment->userID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.commentResponseOwner.notification.mail', array(
			'news' => $news,
			'author' => $this->author,
			'commentAuthor' => $commentAuthor
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
