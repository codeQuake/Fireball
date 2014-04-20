<?php
namespace cms\system\user\notification\event;

use cms\data\page\Page;
use wcf\data\comment\Comment;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class PageCommentResponseUserNotificationEvent extends AbstractUserNotificationEvent {

    public function getTitle() {
        return $this->getLanguage()->get('cms.page.commentResponse.notification.title');
    }

    public function getMessage() {
        $comment = new Comment($this->userNotificationObject->commentID);
        $page = new Page($comment->objectID);
        
        return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.message', array(
            'page' => $page,
            'author' => $this->author
        ));
    }

    public function getEmailMessage($notificationType = 'instant') {
        $comment = new Comment($this->userNotificationObject->commentID);
        $page = new Page($comment->objectID);
        
        return $this->getLanguage()->getDynamicVariable('cms.page.commentResponse.notification.mail', array(
            'page' => $page,
            'author' => $this->author
        ));
    }

    public function getLink() {
        $comment = new Comment($this->userNotificationObject->commentID);
        $page = new Page($comment->objectID);
        
        return LinkHandler::getInstance()->getLink('Page', array(
            'application' => 'cms',
            'object' => $page
        ), '#comments');
    }
}
