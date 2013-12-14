<?php
namespace cms\system\user\notification\object\type;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

class NewsCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
    protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentResponseUserNotificationObject';
    protected static $objectClassName = 'wcf\data\comment\response\CommentResponse';
    protected static $objectListClassName = 'wcf\data\comment\response\CommentResponseList';
}