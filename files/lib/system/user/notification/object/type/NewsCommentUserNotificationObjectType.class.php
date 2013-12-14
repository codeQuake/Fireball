<?php
namespace cms\system\user\notification\object\type;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\WCF;

class NewsCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements ICommentUserNotificationObjectType {
    protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentUserNotificationObject';
    protected static $objectClassName = 'wcf\data\comment\Comment';
    protected static $objectListClassName = 'wcf\data\comment\CommentList';
    public function getOwnerID($objectID) {
		$sql = "SELECT		news.userID
			FROM		wcf".WCF_N."_comment comment
			LEFT JOIN	cms".WCF_N."_news news
			ON		(news.newsID = comment.objectID)
			WHERE		comment.commentID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($objectID));
		$row = $statement->fetchArray();
		
		return ($row ? $row['userID'] : 0);
	}
}