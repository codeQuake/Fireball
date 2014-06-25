<?php
namespace cms\data\content;

use cms\data\content\section\ContentContentSectionList;
use cms\data\page\PageCache;
use cms\data\CMSDatabaseObject;
use cms\system\revision\ContentRevisionHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\poll\Poll;
use wcf\data\IPollObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a content item.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Content extends CMSDatabaseObject implements IRouteController, IPollObject {
	protected static $databaseTableName = 'content';
	protected static $databaseTableIndexName = 'contentID';
	public $poll = null;

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

	public function getPage() {
		return PageCache::getInstance()->getPage($this->pageID);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getIcon() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->getProcessor()->getIcon();
	}

	public function getOutput() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->getProcessor()->getOutput($this);
	}

	public function getCategory() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->category;
	}

	public function getParentContent() {
		if ($this->parentID !== null) return ContentCache::getInstance()->getContent($this->parentID);
		return null;
	}

	//build css structure
	public function getCSSClasses() {
		if ($this->getCategory() == 'structure') {
			if ($this->parentID != null && $this->getParentContent()->getCategory() == 'structure') {
				$childCSS = $this->getParentContent()->getObjectType()->getProcessor()->getChildCSSClasses($this);
				if ($childCSS != '') return $this->getObjectType()->getProcessor()->getCSSClasses().' '.$childCSS.' '.$this->cssClasses;
			}
			return $this->getObjectType()->getProcessor()->getCSSClasses().' '.$this->cssClasses;
		}
		if ($this->parentID != null && $this->getParentContent()->getCategory() == 'structure') {
			$childCSS = $this->getParentContent()->getObjectType()->getProcessor()->getChildCSSClasses($this);
			if ($childCSS != '') return $childCSS.' '.$this->cssClasses;
			return $this->cssClasses;
		}
		return $this->cssClasses;
	}

	public function handleContentData() {
		return @unserialize($this->contentData);
	}

	public function getObjectType() {
		return ObjectTypeCache::getInstance()->getObjectType($this->contentTypeID);
	}

	public function getTypeName() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->objectType;
	}

	public function getPoll() {
		$data = $this->handleContentData();
		if ($data['pollID'] && $this->poll === null) {
			$this->poll = new Poll($data['pollID']);
			$this->poll->setRelatedObject($this);
		}

		return $this->poll;
	}

	public function setPoll(Poll $poll) {
		$this->poll = $poll;
		$this->poll->setRelatedObject($this);
	}

	public function canVote() {
		(WCF::getSession()->getPermission('user.cms.content.canVotePoll') ? true : false);
	}

	public function getRevisions() {
		//gets page revisions
		return ContentRevisionHandler::getInstance()->getRevisions($this->contentID);
	}
}
