<?php
namespace cms\data\page;

use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\revision\PageRevisionList;
use cms\data\stylesheet\StylesheetCache;
use cms\system\page\PagePermissionHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObject;
use wcf\data\ILinkableObject;
use wcf\data\IPermissionObject;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a page.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Page extends DatabaseObject implements IBreadcrumbProvider, ILinkableObject, IPermissionObject, IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'page';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'pageID';

	/**
	 * revisions of this page
	 * @var	array<\cms\data\page\revision\PageRevision>
	 */
	protected $revisions = null;

	/**
	 * @see	\wcf\data\IStorableObject::__get()
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
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		$this->data['additionalData'] = @unserialize($this->data['additionalData']);
		if (!is_array($this->data['additionalData'])) {
			$this->data['additionalData'] = array();
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

		if ($this->isHome) {
			// user can't delete landing page
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

		if (!$this->isPublished && !WCF::getSession()->getPermission('mod.fireball.canReadUnpublishedPage') && !$this->getPermission('canViewUnpublishedPage')) {
			// user can't read unpublished pages
			return false;
		}

		if (!$this->isPublished && $this->getPermission('canViewUnpublishedPage')) {
			// page is not published, but user is allowed to read it
			return true;
		}

		return $this->getPermission('canViewPage');
	}

	/**
	 * @see	\wcf\data\IPermissionObject::checkPermissions()
	 */
	public function checkPermissions(array $permissions) {
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
	 * @see	\wcf\system\breadrcumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb($this->getTitle(), $this->getLink());
	}

	/**
	 * Returns a list of children of this page
	 *
	 * @return	array<\cms\data\page\Page>
	 */
	public function getChildren() {
		$pageList = new PageList();
		$pageList->getConditionBuilder()->add('page.parentID = (?)', array($this->pageID));
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
	 * @return	array<\cms\data\content\DrainedPositionContentNodeTree>
	 */
	public function getContents() {
		$contentListBody = new DrainedPositionContentNodeTree(null, $this->pageID, null, 'body');
		$contentListSidebar = new DrainedPositionContentNodeTree(null, $this->pageID, null, 'sidebar');

		$contentList = array(
			'body' => $contentListBody->getIterator(),
			'sidebar' => $contentListSidebar->getIterator()
		);

		return $contentList;
	}

	/**
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink($appendSession = true) {
		return LinkHandler::getInstance()->getLink('Page', array(
			'application' => 'cms',
			'forceFrontend' => true,
			'appendSession' => $appendSession,
			'alias' => $this->getAlias()
		));
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
	 * @return	array<\cms\data\page\Page>
	 */
	public function getParentPages() {
		if ($this->isChild()) {
			$parentPages = array();
			$parent = $this;

			while ($parent = $parent->getParentPage()) {
				$parentPages[] = $parent;
			}

			$parentPages = array_reverse($parentPages);
			return $parentPages;
		}

		return array();
	}

	/**
	 * @see	\wcf\data\IPermissionObject::getPermission()
	 */
	public function getPermission($permission) {
		$permissions = PagePermissionHandler::getInstance()->getPermission($this);
		if (isset($permissions[$permission])) {
			return $permissions[$permission];
		}

		return WCF::getSession()->getPermission('user.fireball.page.' . $permission);
	}

	/**
	 * Returns all revisions of this page.
	 * 
	 * @return	array<\cms\data\page\revision\PageRevision>
	 */
	public function getRevisions() {
		if ($this->revisions === null) {
			$revisionList = new PageRevisionList();
			$revisionList->getConditionBuilder()->add('page_revision.pageID = ?', array($this->pageID));
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
		$stylesheets = array();
		foreach ($this->getStylesheetIDs() as $stylesheetID) {
			$stylesheets[$stylesheetID] = StylesheetCache::getInstance()->getStylesheet($stylesheetID);
		}

		return $stylesheets;
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
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
		$list->getConditionBuilder()->add('page.parentID = (?)', array($this->pageID));

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
}
