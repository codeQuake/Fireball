<?php
namespace cms\data\news;

use cms\data\category\NewsCategory;
use cms\data\news\image\NewsImage;
use cms\data\CMSDatabaseObject;
use wcf\data\attachment\Attachment;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\poll\Poll;
use wcf\data\IMessage;
use wcf\data\IPollObject;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\bbcode\MessageParser;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\category\CategoryHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\request\IRouteController;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Represents a news.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class News extends CMSDatabaseObject implements IMessage, IRouteController, IBreadcrumbProvider, IPollObject {
	protected static $databaseTableName = 'news';
	protected static $databaseTableIndexName = 'newsID';
	protected $categories = null;
	protected $poll = null;
	protected $categoryIDs = array();

	public function __construct($id, $row = null, $object = null) {
		if ($id !== null) {
			$sql = "SELECT *
                    FROM " . static::getDatabaseTableName() . "
                    WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$id
			));
			$row = $statement->fetchArray();

			if ($row === false) $row = array();
		}

		parent::__construct(null, $row, $object);
	}

	public function getTitle() {
		return $this->subject;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getTags() {
		$tags = TagEngine::getInstance()->getObjectTags('de.codequake.cms.news', $this->newsID, array(
			($this->languageID === null ? LanguageFactory::getInstance()->getDefaultLanguageID() : "")
		));
		return $tags;
	}

	public function getFormattedMessage() {
		AttachmentBBCode::setObjectID($this->newsID);

		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->getMessage(), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}

	public function getAttachments() {
		if (MODULE_ATTACHMENT == 1 && $this->attachments) {
			$attachmentList = new GroupedAttachmentList('de.codequake.cms.news');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array(
				$this->newsID
			));
			$attachmentList->readObjects();
			$attachmentList->setPermissions(array(
				'canDownload' => WCF::getSession()->getPermission('user.cms.news.canDownloadAttachments'),
				'canViewPreview' => WCF::getSession()->getPermission('user.cms.news.canDownloadAttachments')
			));

			AttachmentBBCode::setAttachmentList($attachmentList);
			return $attachmentList;
		}
		return null;
	}

	public function getExcerpt($maxLength = 500) {
		return StringUtil::truncateHTML($this->getFormattedMessage(), $maxLength);
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

	public function getLanguage() {
		if ($this->languageID) return LanguageFactory::getInstance()->getLanguage($this->languageID);

		return null;
	}

	public function getLanguageIcon() {
		return '<img src="' . $this->getLanguage()->getIconPath() . '" alt="" title="' . $this->getLanguage() . '" class="jsTooltip iconFlag" />';
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

			if (! empty($this->categoryIDs)) {
				foreach ($this->categoryIDs as $categoryID) {
					$this->categories[$categoryID] = new NewsCategory(CategoryHandler::getInstance()->getCategory($categoryID));
				}
			}
			else {
				$sql = "SELECT	categoryID
					FROM	cms" . WCF_N . "_news_to_category
					WHERE	newsID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
					$this->newsID
				));
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

	public function isVisible() {
		return true;
	}

	public function canRead() {
		return WCF::getSession()->getPermission('user.cms.news.canViewCategory');
	}

	public function canAdd() {
		return WCF::getSession()->getPermission('user.cms.news.canAddNews');
	}

	public function canModerate() {
		return WCF::getSession()->getPermission('mod.cms.news.canModerateNews');
	}

	public function canSeeDelayed() {
		foreach ($this->getCategories() as $category) {
			if (! $category->getPermission('canViewDelayedNews')) return false;
		}
		return true;
	}

	public function getImage() {
		if ($this->imageID != 0) return new NewsImage($this->imageID);
		return null;
	}

	public static function getIpAddressByAuthor($userID, $username = '', $notIpAddress = '', $limit = 10) {
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("userID = ?", array(
			$userID
		));
		if (! empty($username) && ! $userID) $conditions->add("username = ?", array(
			$username
		));
		if (! empty($notIpAddress)) $conditions->add("ipAddress <> ?", array(
			$notIpAddress
		));
		$conditions->add("ipAddress <> ''");

		$sql = "SELECT		DISTINCT ipAddress
			FROM		cms" . WCF_N . "_news
			" . $conditions . "
			ORDER BY	time DESC";
		$statement = WCF::getDB()->prepareStatement($sql, $limit);
		$statement->execute($conditions->getParameters());

		$ipAddresses = array();
		while ($row = $statement->fetchArray()) {
			$ipAddresses[] = $row['ipAddress'];
		}

		return $ipAddresses;
	}

	public static function getAuthorByIpAddress($ipAddress, $notUserID = 0, $notUsername = '', $limit = 10) {
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("ipAddress = ?", array(
			$ipAddress
		));
		if ($notUserID) $conditions->add("userID <> ?", array(
			$notUserID
		));
		if (! empty($notUsername)) $conditions->add("username <> ?", array(
			$notUsername
		));

		$sql = "SELECT		DISTINCT username, userID
			FROM		cms" . WCF_N . "_news
			" . $conditions . "
			ORDER BY	time DESC";
		$statement = WCF::getDB()->prepareStatement($sql, $limit);
		$statement->execute($conditions->getParameters());

		$users = array();
		while ($row = $statement->fetchArray()) {
			$users[] = $row;
		}

		return $users;
	}

	public function getPoll() {
		if ($this->pollID && $this->poll === null) {
			$this->poll = new Poll($this->pollID);
			$this->poll->setRelatedObject($this);
		}

		return $this->poll;
	}

	public function setPoll(Poll $poll) {
		$this->poll = $poll;
		$this->poll->setRelatedObject($this);
	}

	public function canVote() {
		return (WCF::getSession()->getPermission('user.cms.news.canVotePoll') ? true : false);
	}
}
