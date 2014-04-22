<?php
namespace cms\data\page;

use cms\data\content\ContentAction;
use cms\system\cache\builder\PageCacheBuilder;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemEditor;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class PageAction extends AbstractDatabaseObjectAction implements ISortableAction {
	protected $className = 'cms\data\page\PageEditor';
	protected $permissionsDelete = array(
		'admin.cms.page.canAddPage'
	);
	protected $requireACP = array(
		'delete',
		'setAsHome'
	);

	public function create() {
		$page = parent::create();
		PagePermissionCacheBuilder::getInstance()->reset();
		PageCacheBuilder::getInstance()->reset();
		$menuItem = @unserialize($page->menuItem);
		if (isset($menuItem['has']) && $menuItem['has'] == 1) {
			
			// check if has parents
			$parentItem = '';
			if ($page->isChild()) {
				$parent = $page->getParentPage();
				$temp = @unserialize($parent->menuItem);
				if (isset($temp['has']) && $temp['has'] == 1) {
					if ($temp['id'] != 0) {
						$parentItem = new PageMenuItem($temp['id']);
						$parentItem = $parentItem->menuItem;
					}
				}
			}
			
			// create
			$data = array(
				'isDisabled' => 0,
				'menuItem' => empty($page->title) ? 'cms.page.title' . $page->pageID : $page->getTitle(),
				'menuItemController' => 'cms\page\PagePage',
				'menuItemLink' => 'id=' . $page->pageID,
				'menuPosition' => 'header',
				'className' => 'cms\system\menu\page\CMSPageMenuItemProvider',
				'parentMenuItem' => $parentItem,
				'showOrder' => PageMenuItemEditor::getShowOrder(0, 'header')
			);
			$action = new PageMenuItemAction(array(), 'create', array(
				'data' => $data
			));
			$action->executeAction();
			$returnValues = $action->getReturnValues();
			$menuItem['id'] = $returnValues['returnValues']->menuItemID;
			$menuItem = serialize($menuItem);
			$pageEditor = new PageEditor($page);
			$pageEditor->update(array(
				'menuItem' => $menuItem
			));
		}
		return $page;
	}

	public function update() {
		parent::update();
		PagePermissionCacheBuilder::getInstance()->reset();
		PageCacheBuilder::getInstance()->reset();
		
		// update menu item
		foreach ($this->objectIDs as $objectID) {
			$page = new Page($objectID);
			$menuItem = @unserialize($page->menuItem);
			// update
			if (isset($menuItem['has']) && $menuItem['has'] == 1) {
				if (isset($menuItem['id']) && $menuItem['id'] != 0) {
					$action = new PageMenuItemAction(array(
						$menuItem['id']
					), 'update', array(
						'data' => array(
							'menuItem' => empty($page->title) ? 'cms.page.title' . $page->pageID : $page->title
						)
					));
					$action->executeAction();
				}
				// create new
				else {
					// check if has parents
					$parentItem = '';
					if ($page->isChild()) {
						$parent = $page->getParentPage();
						$temp = @unserialize($parent->menuItem);
						if (isset($temp['has']) && $temp['has'] == 1) {
							if ($temp['id'] != 0) {
								$parentItem = new PageMenuItem($temp['id']);
								$parentItem = $parentItem->menuItem;
							}
						}
					}
					$data = array(
						'isDisabled' => 0,
						'menuItem' => empty($page->title) ? 'cms.page.title' . $page->pageID : $page->getTitle(),
						'menuItemController' => 'cms\page\PagePage',
						'menuItemLink' => 'id=' . $page->pageID,
						'menuPosition' => 'header',
						'className' => 'cms\system\menu\page\CMSPageMenuItemProvider',
						'parentMenuItem' => $parentItem != null ? $parentItem : '',
						'showOrder' => PageMenuItemEditor::getShowOrder(0, 'header')
					);
					$action = new PageMenuItemAction(array(), 'create', array(
						'data' => $data
					));
					$action->executeAction();
					$returnValues = $action->getReturnValues();
					$menuItem['id'] = $returnValues['returnValues']->menuItemID;
					$menuItem = serialize($menuItem);
					$pageEditor = new PageEditor($page);
					$pageEditor->update(array(
						'menuItem' => $menuItem
					));
				}
			}
			// delete if unchecked
			else if (isset($menuItem['id']) && $menuItem['id'] != 0) {
				$action = new PageMenuItemAction(array(
					$menuItem['id']
				), 'delete', array());
				$action->executeAction();
				$menuItem['id'] = 0;
				$menuItem['has'] = 0;
				$menuItem = serialize($menuItem);
				$pageEditor = new PageEditor($page);
				$pageEditor->update(array(
					'menuItem' => $menuItem
				));
			}
		}
	}

	public function delete() {
		PageCacheBuilder::getInstance()->reset();
		// delete all contents beloning to the pages
		foreach ($this->objectIDs as $objectID) {
			$page = new Page($objectID);
			$list = $page->getContentList();
			$contentIDs = array();
			foreach ($list as $content) {
				$contentIDs[] = $content->contentID;
			}
			$action = new ContentAction($contentIDs, 'delete', array());
			$action->executeAction();
		}
		
		// delete menuItem
		$menuItem = @unserialize($page->menuItem);
		if (isset($menuItem['has']) && $menuItem['has'] == 1 && isset($menuItem['id'])) {
			$action = new PageMenuItemAction(array(
				$menuItem['id']
			), 'delete', array());
			$action->executeAction();
		}
		parent::delete();
	}

	public function validateSetAsHome() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.page.canAddPage'
		));
		
		$this->pageEditor = $this->getSingleObject();
		if (! $this->pageEditor->pageID) {
			throw new UserInputException('objectIDs');
		}
		
		else if ($this->pageEditor->isHome) {
			throw new PermissionDeniedException();
		}
	}

	public function setAsHome() {
		$this->pageEditor->setAsHome();
		
		// delete existing menu item
		$menuItem = @unserialize($this->pageEditor->menuItem);
		if ($this->pageEditor->hasMenuItem() && $menuItem['id'] != 0) {
			$action = new PageMenuItemAction(array(
				$menuItem['id']
			), 'delete', array());
			$action->executeAction();
			$menuItem['id'] = 0;
			$menuItem['has'] = 0;
			$menuItem = serialize($menuItem);
			$pageEditor = new PageEditor($this->pageEditor->getDecoratedObject());
			$pageEditor->update(array(
				'menuItem' => $menuItem
			));
		}
		
		// get Home Menu Item
		$sql = "SELECT menuItemID FROM wcf" . WCF_N . "_page_menu_item WHERE menuItemController = ? AND menuItemLink = ''";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			'cms\page\PagePage'
		));
		$row = $statement->fetchArray();
		$item = new PageMenuItem($row['menuItemID']);
		
		$action = new PageMenuItemAction(array(
			$item->menuItemID
		), 'update', array(
			'data' => array(
				'menuItem' => $this->pageEditor->title
			)
		));
		$action->executeAction();
		
		PageCacheBuilder::getInstance()->reset();
	}

	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.page.canAddPage'
		));
		
		if (! isset($this->parameters['data']['structure']) || ! is_array($this->parameters['data']['structure'])) {
			throw new UserInputException('structure');
		}
		
		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		foreach ($this->parameters['data']['structure'] as $parentID => $pageIDs) {
			if ($parentID) {
				if (! isset($pages[$parentID])) {
					throw new UserInputException('structure');
				}
				
				$this->objects[$parentID] = new PageEditor($pages[$parentID]);
			}
			
			$aliases = array();
			foreach ($pageIDs as $pageID) {
				if (! isset($pages[$pageID])) {
					throw new UserInputException('structure');
				}
				if (in_array($pages[$pageID]->alias, $aliases)) {
					throw new UserInputException('structure');
				}
				$aliases[] = $pages[$pageID]->alias;
				
				$this->objects[$pageID] = new PageEditor($pages[$pageID]);
			}
		}
	}

	public function updatePosition() {
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'] as $parentID => $pageIDs) {
			$position = 1;
			foreach ($pageIDs as $pageID) {
				$this->objects[$pageID]->update(array(
					'parentID' => $parentID != 0 ? $this->objects[$parentID]->pageID : null,
					'showOrder' => $position ++
				));
			}
		}
		WCF::getDB()->commitTransaction();
		
		PageCacheBuilder::getInstance()->reset();
	}
}
