<?php
namespace cms\data\page;

use cms\data\content\Content;
use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\revision\PageRevision;
use cms\data\page\revision\PageRevisionList;
use cms\data\stylesheet\StylesheetCache;
use cms\system\page\PagePermissionHandler;
use wcf\data\menu\item\MenuItem;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\page\PageCache as WCFPageCache;
use wcf\data\DatabaseObject;
use wcf\data\IPermissionObject;
use wcf\data\ITitledLinkObject;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a page.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property-read	integer		$pageID		                    id of the page
 * @property-read	integer		$isHome		                    page is set as home
 * @property-read	integer		$authorID		                id of the user who created the page
 * @property-read	string		$authorName		                username of the user who created the page
 * @property-read	integer		$lastEditorID	                id of the user who's edit was the last one
 * @property-read	string		$lastEditorName	                username of the user who's edit was the last one
 * @property-read	integer		$creationTime	                timestamp of creation
 * @property-read	integer		$lastEditTime	                timestamp of last edit
 * @property-read	integer		$comments		                amount of comments
 * @property-read	integer		$clicks		                    amount of clicks since creation/reset
 * @property-read	string		$title		                    title of the page
 * @property-read	string		$alias		                    url-alias of the page
 * @property-read	string		$description    	            a page's description
 * @property-read	string		$metaDescription                a page's meta-description
 * @property-read	string		$metaKeywords		            a page's meta-keywords
 * @property-read	string		$allowIndexing		            page is allowed to be indexed by spiders
 * @property-read	integer		$parentID		                id of the parent page
 * @property-read	integer		$showOrder		                show order of the page
 * @property-read	integer		$invisible		                page is invisible
 * @property-read	integer		$isDisabled		                page is disabled
 * @property-read	integer		$isPublished		            page is published
 * @property-read	integer		$publicationDate	            timestamp of publication (0 if it should be enabled from now on)
 * @property-read	integer		$deactivationDate	            timestamp of deactivation (0 if it shouldn't be disabled)
 * @property-read	integer		$menuItemID		                id of the menu item
 * @property-read	integer		$isCommentable		            page can be commented
 * @property-read	integer		$availableDuringOfflineMode		page is viewable during offline-mode
 * @property-read	integer		$allowSubscribing	            page can be subscribed to
 * @property-read	integer		$styleID		                id of the style the page should use
 * @property-read	string		$sidebarOrientation	            orientation of the sidebar (left|right)
 * @property-read	integer		$objectTypeID		            id of the objecttype of the page type
 * @property-read	array		$additionalData		            additional data
 * @property-read	integer		$wcfPageID		            id of the wcf's page object
 */
class Page extends DatabaseObject implements ITitledLinkObject, IPermissionObject, IRouteController {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'page';

	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'pageID';

	/**
	 * revisions of this page
	 * @var	array<\cms\data\page\revision\PageRevision>
	 */
	protected $revisions = null;

	/**
	 * @var integer
	 */
	protected $latestEditTime = null;

	/**
	 * @inheritDoc
	 */
	public function __get($name) {
		$value = parent::__get($name);
	
		// search additional data if unknown information requested
		if ($value === null) {
			if (isset($this->data['additionalData'][$name])) {
				return $this->data['additionalData'][$name];
			}
		}
	
		return $value;
	}

	/**
	 * @inheritDoc
	 */
	protected function handleData($data) {
		parent::handleData($data);

		$this->data['additionalData'] = @unserialize($this->data['additionalData']);
		if (!is_array($this->data['additionalData'])) {
			$this->data['additionalData'] = [];
		}
	}

	/**
	 * Returns whether the current user can delete this page.
	 * 
	 * @return	boolean
	 */
	public function canDelete() {
		if (!WCF::getSession()->getPermission('admin.fireball.page.canAddPage')) {
			return false;
		}

		if ($this->isHome && PageCache::getInstance()->getPageCount() > 1) {
			// user can't delete landing page if other pages exist
			return false;
		}

		return true;
	}

	/**
	 * Returns whether the current user can read this page.
	 * 
	 * @return	boolean
	 */
	public function canRead() {
		if ($this->isDisabled && !WCF::getSession()->getPermission('mod.fireball.canViewDisabledPage')) {
			// user can't read disabled pages
			return false;
		}

		if (!$this->isPublished && !$this->getPermission('mod.canViewUnpublishedPage')) {
			// user can't read unpublished pages
			return false;
		}

		if (!$this->isPublished && $this->getPermission('mod.canViewUnpublishedPage')) {
			// page is not published, but user is allowed to read it
			return true;
		}

		return $this->getPermission('user.canViewPage');
	}

	/**
	 * @inheritDoc
	 * @param array $permissions
	 */
	public function checkPermissions(array $permissions = ['user.canViewPage']) {
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Returns the 'full' alias including prepended aliases from parent
	 * pages.
	 * 
	 * @return	string
	 */
	public function getAlias() {
		if ($this->getParentPage() !== null) {
			return $this->getParentPage()->getAlias() . '/' . $this->alias;
		}

		return $this->alias;
	}

	/**
	 * Returns a list of children of this page
	 *
	 * @return	array<\cms\data\page\Page>
	 */
	public function getChildren() {
		$pageList = new PageList();
		$pageList->getConditionBuilder()->add('page.parentID = (?)', [$this->pageID]);
		$pageList->readObjects();

		return $pageList->getObjects();
	}

	/**
	 * Returns a list of all descendants of this page.
	 * 
	 * @param	integer		$maxDepth
	 * @return	\RecursiveIteratorIterator
	 */
	public function getChildrenTree($maxDepth = -1) {
		$nodeTree = new AccessiblePageNodeTree($this->pageID);
		$nodeTree->setMaxDepth($maxDepth);

		return $nodeTree->getIterator();
	}

	/**
	 * Returns node trees of all contents that are assigned to this page.
	 * Contents are grouped by their position ('body' and 'sidebar').
	 * 
	 * @return	\cms\data\content\DrainedPositionContentNodeTree[]
	 */
	public function getContents() {
		$availablePositions = Content::AVAILABLE_POSITIONS;
		$contentList = [];

		foreach ($availablePositions as $position) {
			$nodeTree = new DrainedPositionContentNodeTree(null, $this->pageID, null, $position);
			$contentList[$position] = $nodeTree->getIterator();
		}

		return $contentList;
	}

	/**
	 * @inheritDoc
	 */
	public function getLink($appendSession = true) {
		if ($this->isHome) {
			$root = WCFPageCache::getInstance()->getLandingPage();
			if ($root->getApplication()->getAbbreviation() == 'cms' && $root->originIsSystem == 0) {
				return $root->getApplication()->getPageURL();
			}
		}
		
		return LinkHandler::getInstance()->getLink($this->getAlias(), [
			'application' => 'cms',
			'forceFrontend' => true,
			'appendSession' => $appendSession
		]);
	}

	/**
	 * Returns the parent page.
	 * 
	 * @return	\cms\data\page\Page
	 */
	public function getParentPage() {
		if ($this->isChild()) {
			return PageCache::getInstance()->getPage($this->parentID);
		}

		return null;
	}

	/**
	 * Returns the most parental page.
	 * 
	 * @return	\cms\data\page\Page
	 */
	public function getRootPage() {
		$page = $this;
		while ($page->isChild()) {
			$page = PageCache::getInstance()->getPage($page->parentID);
		}
		
		return $page;
	}

	/**
	 * Returns the parent pages of this page.
	 *
	 * @param       boolean $invertArray
	 * @return	\cms\data\page\Page[]
	 */
	public function getParentPages($invertArray = true) {
		if ($this->isChild()) {
			$parentPages = [];
			$parent = $this;

			while ($parent = $parent->getParentPage()) {
				$parentPages[] = $parent;
			}

			if ($invertArray) $parentPages = array_reverse($parentPages);
			return $parentPages;
		}

		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getPermission($permission) {
		$permissions = PagePermissionHandler::getInstance()->getPermissions($this);

		$aclPermission = str_replace(['user.', 'mod.', 'admin.'], ['', '', ''], $permission);
		if (isset($permissions[$aclPermission])) {
			return $permissions[$aclPermission];
		}

		$globalPermission = str_replace(['user.', 'mod.', 'admin.'], ['user.fireball.page.', 'mod.fireball.', 'user.fireball.page.'], $permission);
		return WCF::getSession()->getPermission($globalPermission);
	}

	/**
	 * Returns all revisions of this page.
	 * 
	 * @return	array<\cms\data\page\revision\PageRevision>
	 */
	public function getRevisions() {
		if ($this->revisions === null) {
			$revisionList = new PageRevisionList();
			$revisionList->getConditionBuilder()->add('page_revision.pageID = ?', [$this->pageID]);
			$revisionList->readObjects();

			$this->revisions = $revisionList->getObjects();
		}

		return $this->revisions;
	}

	/**
	 * Returns the ids of the stylesheets of this page.
	 * 
	 * @return	array<integer>
	 */
	public function getStylesheetIDs() {
		return PageCache::getInstance()->getStylesheetIDs($this->pageID);
	}

	/**
	 * Returns the stylesheets of this page.
	 * 
	 * @return	array<integer>
	 */
	public function getStylesheets() {
		$stylesheets = [];
		foreach ($this->getStylesheetIDs() as $stylesheetID) {
			$stylesheets[$stylesheetID] = StylesheetCache::getInstance()->getStylesheet($stylesheetID);
		}

		return $stylesheets;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	/**
	 * Returns whether the current user can access this page.
	 * 
	 * @deprecated	use \cms\data\page\Page::canRead() instead
	 * @return	boolean
	 */
	public function isAccessible() {
		return $this->canRead();
	}

	/**
	 * Returns whether this page is a child of an other page.
	 * 
	 * @return	boolean
	 */
	public function isChild() {
		if ($this->parentID) {
			return true;
		}

		return false;
	}

	/**
	 * Returns whether this page has other pages assigned as children
	 * 
	 * @return	boolean
	 */
	public function hasChildren() {
		$list = new PageList();
		$list->getConditionBuilder()->add('page.parentID = (?)', [$this->pageID]);

		if ($list->countObjects() != 0) {
			return true;
		}

		return false;
	}
	
	/**
	 * Returns the object type of the page
	 * 
	 * @return \wcf\data\object\type\ObjectType
	 */
	public function getObjectType() {
		return ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
	}
	
	public function getTypeName() {
		$this->objectType = $this->getObjectType();
		return $this->objectType->objectType;
	}

	public function getMenuItem() {
		if ($this->menuItemID === null)
			return null;

		return new MenuItem($this->menuItemID);
	}

	/**
	 * Returns the processor of the page
	 *
	 * @return \cms\system\page\type\AbstractPageType
	 */
	public function getProcessor() {
		return ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID)->getProcessor();
	}

	/**
	 * Get the timestamp pf the latest edit (includes also edits of contents)
	 * @return integer
	 */
	public function getLastEditTime() {
		if ($this->latestEditTime === null) {
			$revisions = $this->getRevisions();
			/** @var PageRevision $latestRevision */
			$latestRevision = end($revisions);
			if ($latestRevision !== null)
				$this->latestEditTime = $latestRevision->time;
			else
				$this->latestEditTime = 0;
		}

		return $this->latestEditTime;
	}
	
	/**
	 * @see	\wcf\data\IStorableObject::getData()
	 */
	public function getObjectData() {
		return $this->data;
	}
}
