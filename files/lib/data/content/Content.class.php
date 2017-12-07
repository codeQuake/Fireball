<?php
namespace cms\data\content;

use cms\data\page\PageCache;
use cms\system\content\ContentPermissionHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\poll\Poll;
use wcf\data\DatabaseObject;
use wcf\data\IPermissionObject;
use wcf\data\IPollObject;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a content item.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property-read	integer		$contentID		id of the content
 * @property-read	integer		$parentID		id of the parent content
 * @property-read	integer		$pageID		    id of the page
 * @property-read	string		$title		    title of the content
 * @property-read	integer		$contentTypeID	id of the objecttype of the contenttype
 * @property-read	array		$contentData	content data
 * @property-read	integer		$showOrder		show order of the content
 * @property-read	integer		$isDisabled		content is disabled
 * @property-read	string		$position		position of the content (body|sidebar|sidebarLeft|sidebarRight)
 * @property-read	string		$cssClasses		css classes of the content
 * @property-read	array		$additionalData	additional data
 */
class Content extends DatabaseObject implements IRouteController, IPollObject, IPermissionObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'content';

	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'contentID';
	
	/**
	 * @var Poll|null
	 */
	public $poll = null;
	
	/**
	 * @var string[]
	 */
	const AVAILABLE_POSITIONS = ['hero', 'headerBoxes', 'top', 'sidebarLeft', 'body', 'sidebarRight', 'bottom', 'footerBoxes', 'footer'];
	
	/**
	 * @var \wcf\data\object\type\ObjectType
	 */
	protected $objectType = null;

	/**
	 * @inheritDoc
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
		if ($this->isDisabled && !$this->getPermission('mod.canViewDisabledContent')) {
			// user can't read disabled contents
			return false;
		}

		return $this->getPermission('user.canViewContent');
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
	 * @inheritDoc
	 */
	public function getTitle() {
		if ($this->title !=  '') return WCF::getLanguage()->get($this->title);
		else {
			$this->objectType = $this->getObjectType();
			$htmlOutputProcessor = new HtmlOutputProcessor();
			$htmlOutputProcessor->setOutputType('text/plain');
			$title = $this->objectType->getProcessor()->getPreview($this) ?: 'Content #' . $this->contentID;
			$htmlOutputProcessor->process($title, 'de.codequake.cms.content.type.text', $this->contentID);
			return $htmlOutputProcessor->getHtml();
		}
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
	 * @param       boolean $sortableOutput
	 * @return	string
	 */
	public function getOutput($sortableOutput = false) {
		$this->objectType = $this->getObjectType();
		if ($sortableOutput)
			return $this->objectType->getProcessor()->getSortableOutput($this);
		else
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
	
	/**
	 * @return \wcf\data\object\type\ObjectType
	 */
	public function getObjectType() {
		return ObjectTypeCache::getInstance()->getObjectType($this->contentTypeID);
	}
	
	/**
	 * Returns the name of the content type
	 * @param boolean $short
	 * @return string
	 */
	public function getTypeName($short = false) {
		$this->objectType = $this->getObjectType();
		return $short ? WCF::getLanguage()->get('cms.acp.content.type.' . $this->objectType->objectType) : $this->objectType->objectType;
	}
	
	/**
	 * @return null|\wcf\data\poll\Poll
	 */
	public function getPoll() {
		if ($this->pollID && $this->poll === null) {
			$this->poll = new Poll($this->pollID);
			$this->poll->setRelatedObject($this);
		}

		return $this->poll;
	}
	
	/**
	 * @param \wcf\data\poll\Poll $poll
	 */
	public function setPoll(Poll $poll) {
		$this->poll = $poll;
		$this->poll->setRelatedObject($this);
	}
	
	/**
	 * @return boolean
	 */
	public function canVote() {
		return (WCF::getSession()->getPermission('user.fireball.content.canVotePoll') ? true : false);
	}

	/**
	 * @inheritDoc
	 */
	protected function handleData($data) {
		parent::handleData($data);

		$this->data['contentData'] = @unserialize($this->data['contentData']);
		if (!is_array($this->data['contentData'])) {
			$this->data['contentData'] = [];
		}
	}

	/**
	 * @inheritDoc
	 */
	public function checkPermissions(array $permissions = ['user.canViewContent']) {
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPermission($permission) {
		$permissions = ContentPermissionHandler::getInstance()->getPermissions($this);

		$aclPermission = str_replace(['user.', 'mod.', 'admin.'], ['', '', ''], $permission);
		if (isset($permissions[$aclPermission])) {
			return $permissions[$aclPermission];
		}

		if ($permission == 'user.canViewContent') {
			return $this->getPage()->canRead();
		}

		$globalPermission = str_replace(['user.', 'mod.', 'admin.'], ['user.fireball.content.', 'mod.fireball.', 'user.fireball.content.'], $permission);
		return WCF::getSession()->getPermission($globalPermission);
	}
	
	/**
	 * @see	\wcf\data\IStorableObject::getData()
	 */
	public function getObjectData() {
		return $this->data;
	}
}
