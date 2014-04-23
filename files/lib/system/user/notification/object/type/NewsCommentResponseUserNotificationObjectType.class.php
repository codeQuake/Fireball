<?php
namespace cms\system\user\notification\object\type;

use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentResponseUserNotificationObject';
	protected static $objectClassName = 'wcf\data\comment\response\CommentResponse';
	protected static $objectListClassName = 'wcf\data\comment\response\CommentResponseList';
}
