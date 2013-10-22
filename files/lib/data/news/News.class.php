<?php
namespace cms\data\news;
use wcf\system\request\IRouteController;
use wcf\system\bbcode\MessageParser;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\data\attachment\Attachment;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\IMessage;
use wcf\system\bbcode\AttachmentBBCode;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

class News extends CMSDatabaseObject implements IMessage, IRouteController, IBreadcrumbProvider{

    protected static $databaseTableName = 'news';
    protected static $databaseTableIndexName = 'newsID';
    protected $categories = null;
    protected $categoryIDs = array();
    
    public function __construct($id, $row = null, $object = null){
        if ($id !== null) {
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
    
    public function getTitle(){
        return $this->subject;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getFormattedMessage() {

        AttachmentBBCode::setObjectID($this->newsID);

        MessageParser::getInstance()->setOutputType('text/html');
        return MessageParser::getInstance()->parse($this->getMessage(), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
    }
    
    public function getSimplifiedFormattedMessage() {
        MessageParser::getInstance()->setOutputType('text/simplified-html');
        return MessageParser::getInstance()->parse($this->getMessage(), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
    }
    
    public function getAttachments() {
        if (MODULE_ATTACHMENT == 1 && $this->attachments) {
            $attachmentList = new GroupedAttachmentList('de.codequake.cms.news');
            $attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($this->newsID));
            $attachmentList->readObjects();
            $attachmentList->setPermissions(array(
                'canDownload' => WCF::getSession()->getPermission('user.cms.news.canViewAttachments'),
                'canViewPreview' => WCF::getSession()->getPermission('user.cms.news.canViewAttachments')
            ));

            AttachmentBBCode::setAttachmentList($attachmentList);
            return $attachmentList;
        }
        return null;
    }
    
    public function getExcerpt($maxLength = 255) {
        return StringUtil::truncateHTML($this->getSimplifiedFormattedMessage(), $maxLength);
    }
    
    public function getUserID() {
        return $this->userID;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getTime() {
        return $this->time;
    }
    
    public function getLink() {
        return LinkHandler::getInstance()->getLink('News', array(
            'application' => 'cms',
            'object' => $this,
            'forceFrontend' => true
        ));
    }
    
    public function __toString() {
        return $this->getFormattedMessage();
    }
    
    public function getBreadcrumb() {
        return new Breadcrumb($this->subject, $this->getLink());
    }
    
    public function getCategoryIDs() {
        return $this->categoryIDs;  
       
    }
    
    public function setCategoryID($categoryID) {
        $this->categoryIDs[] = $categoryID;
    }
    
    public function setCategoryIDs(array $categoryIDs) {
        $this->categoryIDs = $categoryIDs;
    }
    
    public function getCategories() {
		if ($this->categories === null) {
			$this->categories = array();
			
			if (!empty($this->categoryIDs)) {
				foreach ($this->categoryIDs as $categoryID) {
					$this->categories[$categoryID] = new NewsCategory(CategoryHandler::getInstance()->getCategory($categoryID));
				}
			}
			else {
				$sql = "SELECT	categoryID
					FROM	cms".WCF_N."_news_to_category
					WHERE	newsID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array($this->newsID));
				while ($row = $statement->fetchArray()) {
					$this->categories[$row['categoryID']] = new NewsCategory(CategoryHandler::getInstance()->getCategory($row['categoryID']));
				}
			}
		}
		
		return $this->categories;
	}
    
    public function getIpAddress() {
		if ($this->ipAddress) {
			return UserUtil::convertIPv6To4($this->ipAddress);
		}
		
		return '';
	}
    
    public function isVisible(){
        return true;
    }
}