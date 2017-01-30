<?php
namespace cms\system\user\notification\object\type;

use wcf\data\comment\Comment;
use wcf\data\comment\CommentList;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\CommentUserNotificationObject;

/**
 * Page comment notification object type.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritDoc
	 */
	protected static $decoratorClassName = CommentUserNotificationObject::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectClassName = Comment::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectListClassName = CommentList::class;
}
