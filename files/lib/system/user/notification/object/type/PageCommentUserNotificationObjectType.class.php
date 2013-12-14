<?php
namespace cms\system\user\notification\object\type;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\WCF;

class PageCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements ICommentUserNotificationObjectType {
    protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentUserNotificationObject';
    protected static $objectClassName = 'wcf\data\comment\Comment';
    protected static $objectListClassName = 'wcf\data\comment\CommentList';
    public function getOwnerID($objectID) { return 0; }
}