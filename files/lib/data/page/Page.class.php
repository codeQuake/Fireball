<?php
namespace cms\data\page;

use cms\data\content\PageContentList;
use cms\data\CMSDatabaseObject;
use cms\system\layout\LayoutHandler;
use cms\system\page\PagePermissionHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class Page extends CMSDatabaseObject implements IRouteController {
	protected static $databaseTableName = 'page';
	protected static $databaseTableIndexName = 'pageID';

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
		if (preg_match('#cms.page.title#', $this->title)) return WCF::getLanguage()->get($this->title);
		return $this->title;
	}

	public function getLayout() {
		if ($this->layoutID != 0) {
			return LayoutHandler::getInstance()->getStylesheet($this->layoutID);
		}
		
		return '';
	}

	public function isVisible() {
		if ($this->invisible == 1 && $this->getPermission('canViewInvisiblePage')) return true;
		if ($this->invisible == 0 && $this->getPermission('canViewPage')) return true;
		return false;
	}

	public function isAccessible() {
		if ($this->getPermission('canEnterPage')) return true;
		return false;
	}

	public function isChild() {
		if ($this->parentID) return true;
		return false;
	}

	public function hasChildren() {
		$list = new PageList();
		$list->getConditionBuilder()->add('page.parentID = (?)', array(
			$this->pageID
		));
		if ($list->countObjects() != 0) return true;
		return false;
	}

	public function getChildren() {
		$list = new PageList();
		$list->getConditionBuilder()->add('page.parentID = (?)', array(
			$this->pageID
		));
		$list->readObjects();
		$list = $list->getObjects();
		return $list;
	}
	
	// builds up a complete folder structure like link
	public function getAlias() {
		// returns page alias
		if ($this->getParentPage() != null) {
			return $this->getParentPage()->getAlias() . '/' . $this->alias;
		}
		
		return $this->alias;
	}

	public function getLink() {
		return LinkHandler::getInstance()->getLink('Page', array(
			'application' => 'cms',
			'forceFrontend' => true,
			'alias' => $this->getAlias()
		));
	}

	public function hasMenuItem() {
		$menuItem = @unserialize($this->menuItem);
		if (isset($menuItem['has']) && $menuItem['has'] != 0) return true;
		return false;
	}

	public function getParentPage() {
		if ($this->isChild()) {
			return new Page($this->parentID);
		}
		return null;
	}

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

	public function getContentList($position = 'body') {
		$list = new PageContentList($this->pageID);
		$list->getConditionBuilder()->add('content.position = ?', array(
			$position
		));
		$list->readObjects();
		return $list->getObjects();
	}

	public function getPermission($permission = 'canViewPage') {
		$permissions = PagePermissionHandler::getInstance()->getPermission($this);
		if (isset($permissions[$permission])) {
			return $permissions[$permission];
		}
		return WCF::getSession()->getPermission('user.cms.page.' . $permission);
	}

	public function checkPermission(array $permissions = array('canViewPage')) {
		foreach ($permissions as $permission) {
			if (! $this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}
}
