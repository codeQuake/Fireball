<?php
namespace cms\data\content;

use cms\data\page\PageCache;
use cms\data\CMSDatabaseObject;
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
	 * @see	\wcf\data\IStorableObject::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);

		// search content data if unknown information requested
		if ($value === null) {
			if (isset($this->data['contentData'][$name])) {
				return $this->data['contentData'][$name];
			}
		}

		return $value;
	}

	/**
	 * Returns whether the current user can read this content.
	 * 
	 * Notice: This function does NOT check whether the user actually can
	 * access the assigned page. Make sure to call `canRead` of the page
	 * object along with this function.
	 * 
	 * @return	boolean
	 */
	public function canRead() {
		if ($this->isDisabled && !WCF::getSession()->getPermission('mod.cms.canViewDisabledContent')) {
			// user can't read disabled contents
			return false;
		}

		return true;
	}

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

	/**
	 * Returns the icon name (with icon prefix) for this content.
	 * 
	 * @return	string
	 */
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

	/**
	 * Returns the parent content object or null if no parent content
	 * exists.
	 * 
	 * @return	\cms\data\content\Content
	 */
	public function getParentContent() {
		if ($this->parentID !== null) return ContentCache::getInstance()->getContent($this->parentID);
		return null;
	}

	/**
	 * Returns all css classes for this content.
	 * 
	 * @return	string
	 */
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

	public function getObjectType() {
		return ObjectTypeCache::getInstance()->getObjectType($this->contentTypeID);
	}

	public function getTypeName() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->objectType;
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
		return (WCF::getSession()->getPermission('user.cms.content.canVotePoll') ? true : false);
	}

	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		$this->data['contentData'] = @unserialize($this->data['contentData']);
		if (!is_array($this->data['contentData'])) {
			$this->data['contentData'] = array();
		}
	}
}
