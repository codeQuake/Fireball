<?php
namespace cms\data\content;
use cms\data\CMSDatabaseObject;
use wcf\data\IMessage;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;
use wcf\system\request\LinkHandler;

class Content extends CMSDatabaseObject implements IMessage{
    protected static $databaseTableName = 'content';
    protected static $databaseTableIndexName = 'contentID';
    
    public function __construct($id, $row = null, $object = null){
        if($id !== null){
            $sql = "SELECT *
                    FROM ".static::getDatabaseTableName()."
                    WHERE (".static::getDatabaseTableIndexName()." = ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array($id));
            $row = $statement->fetchArray();
            if ($row === false) $row = array();
        }
        parent::__construct(null, $row, $object);
    }
    
    public function getFormattedMessage(){
        MessageParser::getInstance()->setOutputType('text/html');
        return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
    }
    
    public function getTitle(){
        return $this->subject;
    }
    
    public function getMessage(){
        return $this->message;
    }
    
    public function __toString(){
        return $this->getFormattedMessage();
    }
    
    public function getExcerpt($maxLength = 255){
        MessageParser::getInstance()->setOutputType('text/simplified-html');
        return StringUtil::truncateHTML(MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes), $maxLength);
    }
    
    public function getLink(){
        return LinkHandler::getInstance()->getLink(null, array(
                                                                'application' => 'cms',
                                                                'object' => $this));
    }
    
    public function isVisible(){
        return true;
    }
    
    public function getTime(){
        return $this->time;
    }
    public function getUserID(){
        return $this->userID;
    }
    public function getUsername(){
        return $this->username;
    }
}