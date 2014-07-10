<?php
namespace cms\data\page;

use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\system\cache\builder\PageCacheBuilder;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use cms\system\revision\PageRevisionHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\AJAXException;
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

	protected $resetCache = array(
		'create',
		'delete',
		'toggle',
		'update',
		'updatePosition',
		'setAsHome',
		'restoreRevision',
		'copy'
	);

	protected $permissionsDelete = array(
		'admin.cms.page.canAddPage'
	);

	protected $permissionsUpdate = array(
		'admin.cms.page.canAddPage'
	);

	protected $requireACP = array(
		'delete',
		'setAsHome'
	);

	public function create() {
		$page = parent::create();

		//check if first page
		if (PageCache::getInstance()->getHomePage() === null) {
			$editor = new PageEditor($page);
			$editor->setAsHome();
		}

		PagePermissionCacheBuilder::getInstance()->reset();
		$this->objects = array(
			new PageEditor($page)
		);
		return $page;
	}

	public function delete() {
		// delete all contents belonging to the pages
		foreach ($this->objectIDs as $objectID) {
			$page = new Page($objectID);
			$list = $page->getContents();
			$contentIDs = array();
			foreach ($list['body'] as $content) {
				$contentIDs[] = $content->contentID;
			}
			foreach ($list['sidebar'] as $content) {
				$contentIDs[] = $content->contentID;
			}
			$action = new ContentAction($contentIDs, 'delete', array());
			$action->executeAction();

			$action = new PageMenuItemAction(array(
				$page->menuItemID
			), 'delete', array());
			$action->executeAction();
		}

		parent::delete();

		//check if first page
		PageCacheBuilder::getInstance()->reset();
		if (PageCache::getInstance()->getHomePage() === null) {
			$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
			$page = reset($pages);
			if ($page != null) {
				$editor = new PageEditor($page);
				$editor->setAsHome();
			}
		}
	}

	protected function createRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		$action = 'create';
		if (isset($this->parameters['action'])) {
			$action = $this->parameters['action'];
		}

		foreach ($this->objects as $object) {
			call_user_func(array(
				$this->className,
				'createRevision'
			), array(
				'pageID' => $object->getObjectID(),
				'action' => $action,
				'userID' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				'time' => TIME_NOW,
				'data' => serialize($object->getDecoratedObject()->getData())
			));
		}
	}

	public function validateRestoreRevision() {
		parent::validateUpdate();
	}

	public function restoreRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $object) {
			$restoreObject = PageRevisionHandler::getInstance()->getRevisionByID($object->pageID, $this->parameters['restoreObjectID']);
			$this->parameters['data'] = @unserialize($restoreObject->data);
		}

		$this->update();
	}

	public function validateGetRevisions() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	public function getRevisions() {
		$objectID = reset($this->objectIDs);
		$page = PageCache::getInstance()->getPage($objectID);
		$revisions = $page->getRevisions();

		WCF::getTPL()->assign(array(
			'revisions' => $revisions,
			'pageID' => $page->pageID
		));

		return array(
			'template' => WCF::getTPL()->fetch('pageRevisionList', 'cms'),
			'revisions' => $revisions,
			'pageID' => $page->pageID
		);
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

		//create revision
		$this->parameters['action'] = 'setAsHome';
		$this->createRevision();
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
					throw new AJAXException(WCF::getLanguage()->get('cms.acp.page.alias.error.sort', 412));
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

		//create revision
		$this->parameters['action'] = 'updatePosition';
		$this->createRevision();
	}

	public function validateGetContentTypes() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
		if (! isset($this->parameters['position'])) $this->parameters['position'] = 'body';
	}

	public function getContentTypes() {
		$types = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');
		$categories = array();
		foreach ($types as $type) {
			$categories[$type->category] = array();
		}
		foreach ($types as $type) {
			if ($this->parameters['position'] == 'body' && $type->allowcontent) array_push($categories[$type->category], $type);
			if ($this->parameters['position'] == 'sidebar' && $type->allowsidebar) array_push($categories[$type->category], $type);
		}

		WCF::getTPL()->assign(array(
			'pageID' => reset($this->objectIDs),
			'contentTypes' => $categories,
			'position' => $this->parameters['position'],
			'parentID' => isset($this->parameters['parentID']) ? intval($this->parameters['parentID']) : null
		));

		return array(
			'template' => WCF::getTPL()->fetch('contentTypeList', 'cms'),
			'pageID' => reset($this->objectIDs),
			'position' => $this->parameters['position'],
			'parentID' => isset($this->parameters['parentID']) ? intval($this->parameters['parentID']) : null
		);
	}

	public function validateCopy() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		if (count($this->objects) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	public function copy() {
		$object = reset($this->objects);
		$data = $object->getDecoratedObject()->getData();
		$data['alias'] .= '-copy';
		unset($data['pageID']);
		unset($data['isHome']);
		$this->parameters['data'] = $data;
		$page = $this->create();
		$pageID = $page->pageID;
		$contents = $object->getContents();
		$tmp = array();
		//body
		foreach ($contents['body'] as $content) {
			//recreate
			$data = $content->getDecoratedObject()->getData();
			$oldID = $data['contentID'];
			unset($data['contentID']);
			unset($data['isHome']);
			$data['pageID'] = $pageID;
			$action = new ContentAction(array(), 'create', array(
				'data' => $data
			));
			$return = $action->executeAction();
			$tmp[$oldID] = $return['returnValues']->contentID;
		}

		//sidebar
		foreach ($contents['sidebar'] as $content) {
			//recreate
			$data = $content->getData();
			$oldID = $data['contentID'];
			unset($data['contentID']);
			$data['pageID'] = $pageID;
			$action = new ContentAction(array(), 'create', array(
				'data' => $data
			));
			$return = $action->executeAction();
			$tmp[$oldID] = $return['returnValues']->contentID;
		}

		//clear cache

		//setting new IDs
		$contents = new ContentList();
		$contents->getConditionBuilder()->add('pageID = ?', array(
			$pageID
		));
		$contents->readObjects();
		$contents = $contents->getObjects();
		foreach ($contents as $content) {
			$update = array();
			if (isset($tmp[$content->parentID])) {
				$editor = new ContentEditor($content);
				$update['parentID'] = $tmp[$content->parentID];
				$editor->update($update);
			}
		}
	}
}
