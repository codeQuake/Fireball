<?php
namespace cms\data\page;

use cms\data\content\ContentAction;
use cms\system\cache\builder\PageCacheBuilder;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\object\type\ObjectTypeCache;
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
 * Executes page-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
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
		return $page;
	}

	public function update() {
		parent::update();
		PagePermissionCacheBuilder::getInstance()->reset();
		PageCacheBuilder::getInstance()->reset();

	}

	public function delete() {
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


		$action = new PageMenuItemAction(array(
			$page->menuItemID
		), 'delete', array());
		$action->executeAction();

		parent::delete();
		PageCacheBuilder::getInstance()->reset();
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

	public function validateGetContentTypes() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
		if (!isset($this->parameters['position'])) $this->parameters['position'] = 'body';
	}

	public function getContentTypes() {
		$types = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');
		$categories = array();
		foreach ($types as $type) {
			$categories[$type->category] = array();
		}
		foreach ($types as $type) {
			if ($this->parameters['position'] == 'body' && $type->allowcontent)	array_push($categories[$type->category], $type);
			if ($this->parameters['position'] == 'sidebar' && $type->allowsidebar)	array_push($categories[$type->category], $type);
		}

		WCF::getTPL()->assign(array(
			'pageID' => reset($this->objectIDs),
			'contentTypes' => $categories,
			'position' => $this->parameters['position'],
			'parentID' => intval($this->parameters['parentID'])));

		return array(
			'template' => WCF::getTPL()->fetch('contentTypeList', 'cms'),
			'pageID' => reset($this->objectIDs),
			'position' => $this->parameters['position'],
			'parentID' => intval($this->parameters['parentID'])
		);
	}
}
