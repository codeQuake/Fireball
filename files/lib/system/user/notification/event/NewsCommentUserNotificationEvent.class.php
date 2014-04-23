<?php
namespace cms\system\user\notification\event;

use cms\data\news\News;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentUserNotificationEvent extends AbstractUserNotificationEvent {

	public function getTitle() {
		return $this->getLanguage()->get('cms.news.comment.notification.title');
	}

	public function getMessage() {
		$news = new News($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.comment.notification.message', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	public function getEmailMessage($notificationType = 'instant') {
		$news = new News($this->userNotificationObject->objectID);
		
		return $this->getLanguage()->getDynamicVariable('cms.news.comment.notification.mail', array(
			'news' => $news,
			'author' => $this->author
		));
	}

	public function getLink() {
		$news = new News($this->userNotificationObject->objectID);
		
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $news
		), '#comments');
	}
}
