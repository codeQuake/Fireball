<?php
namespace cms\system\user\notification\object\type;

use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\response\CommentResponseList;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;

/**
 * Page comment response notification object type.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritDoc
	 */
	protected static $decoratorClassName = CommentResponseUserNotificationObject::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectClassName = CommentResponse::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectListClassName = CommentResponseList::class;
}
