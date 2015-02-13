<?php
namespace cms\data\content;

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
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Content extends CMSDatabaseObject implements IRouteController, IPollObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'content';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'contentID';

	public $poll = null;

	/**
	 * Returns the page this content is assigned to.
	 * 
	 * @return	\cms\data\page\Page
	 */
	public function getPage() {
		return PageCache::getInstance()->getPage($this->pageID);
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	/**
	 * Returns a list of all children of this content
	 * 
	 * @return	\RecursiveIteratorIterator
	 */
	public function getChildren() {
		$contentNodeTree = new ContentNodeTree($this->contentID);
		return $contentNodeTree->getIterator();
	}

	public function getIcon() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->getProcessor()->getIcon();
	}

	/**
	 * Returns the formatted output for this content.
	 * 
	 * @return	string
	 */
	public function getOutput() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->getProcessor()->getOutput($this);
	}

	/**
	 * Returns the category string of this content.
	 * 
	 * @return	string
	 */
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
				if ($childCSS != '') {
					return $this->getObjectType()->getProcessor()->getCSSClasses() . ' ' . $childCSS . ' ' . $this->cssClasses;
				}
			}

			return $this->getObjectType()->getProcessor()->getCSSClasses() . ' ' . $this->cssClasses;
		}

		if ($this->parentID != null && $this->getParentContent()->getCategory() == 'structure') {
			$childCSS = $this->getParentContent()->getObjectType()->getProcessor()->getChildCSSClasses($this);
			if ($childCSS != '') {
				return $childCSS . ' ' . $this->cssClasses;
			}

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
		return (WCF::getSession()->getPermission('user.cms.content.canVotePoll') ? true : false);
	}

	public function getRevisions() {
		//gets content revisions
		return ContentRevisionHandler::getInstance()->getRevisions($this->contentID);
	}
}
