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
use wcf\data\IClipboardAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
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
class PageAction extends AbstractDatabaseObjectAction implements IClipboardAction, ISortableAction, IToggleAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\page\PageEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.page.canAddPage');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.cms.page.canAddPage');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete', 'disable', 'enable', 'setAsHome');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$resetCache
	 */
	protected $resetCache = array('copy', 'create', 'delete', 'disable', 'enable', 'restoreRevision', 'setAsHome', 'toggle', 'update', 'updatePosition');

	/**
	 * Validates parameters to copy a page.
	 */
	public function validateCopy() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		if (count($this->objects) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	/**
	 * Copies a specific page.
	 */
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

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// set default values for author and last editor
		if (!isset($this->parameters['data']['authorID'])) {
			$this->parameters['data']['authorID'] = WCF::getUser()->userID;
			$this->parameters['data']['authorName'] = WCF::getUser()->username;
		}
		if (!isset($this->parameters['data']['lastEditorID'])) {
			$this->parameters['data']['lastEditorID'] = $this->parameters['data']['authorID'];
			$this->parameters['data']['lastEditorName'] = $this->parameters['data']['authorName'];
		}

		// set default values for creation- and last edit time
		if (!isset($this->parameters['data']['creationTime'])) {
			$this->parameters['data']['creationTime'] = TIME_NOW;
		}
		if (!isset($this->parameters['data']['lastEditTime'])) {
			$this->parameters['data']['lastEditTime'] = $this->parameters['data']['creationTime'];
		}

		// create page itself
		$page = parent::create();
		$pageEditor = new PageEditor($page);

		// check if first page
		if (PageCache::getInstance()->getHomePage() === null) {
			$pageEditor->setAsHome();
		}

		PagePermissionCacheBuilder::getInstance()->reset();
		$this->objects = array($pageEditor);

		return $page;
	}

	/**
	 * Creates a new revision for pages.
	 */
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

	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
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

	/**
	 * Validates permissions to disable pages.
	 */
	public function validateDisable() {
		$this->validateUpdate();
	}

	/**
	 * Disables pages.
	 */
	public function disable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $pageEditor) {
			$pageEditor->update(array('isDisabled' => 1));
		}
	}

	/**
	 * Validates permissions to enable pages.
	 */
	public function validateEnable() {
		$this->validateUpdate();
	}

	/**
	 * Enables pages.
	 */
	public function enable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $pageEditor) {
			$pageEditor->update(array('isDisabled' => 0));
		}
	}

	/**
	 * Validates parameters to get a rendered list of content types.
	 */
	public function validateGetContentTypes() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
		if (! isset($this->parameters['position'])) $this->parameters['position'] = 'body';
	}

	/**
	 * Returns a rendered list of content types.
	 */
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

	/**
	 * Validates permissions and parameters for page revision list.
	 */
	public function validateGetRevisions() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	/**
	 * Returns a rendered list of page revisions.
	 */
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

	/**
	 * Validates permissions to restore a revision.
	 */
	public function validateRestoreRevision() {
		$this->validateUpdate();
	}

	/**
	 * Restores a specific revision.
	 */
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

	/**
	 * Validates permissions and parameters to set a page ad home.
	 */
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

	/**
	 * Sets a specific page as home.
	 */
	public function setAsHome() {
		$this->pageEditor->setAsHome();

		// create revision
		$this->parameters['action'] = 'setAsHome';
		$this->createRevision();
	}

	/**
	 * @see	\wcf\data\IToggleAction::validateToggle()
	 */
	public function validateToggle() {
		$this->validateUpdate();
	}

	/**
	 * @see	\wcf\data\IToggleAction::toggle()
	 */
	public function toggle() {
		if (empty($this->objects)) $this->readObjects();

		foreach ($this->objects as $page) {
			$page->update(array(
				'isDisabled' => 1 - $page->isDisabled
			));
		}
	}

	/**
	 * @see	\wcf\data\IClipboardAction::validateUnmarkAll()
	 */
	public function validateUnmarkAll() { /* nothing */ }

	/**
	 * @see	\wcf\data\IClipboardAction::unmarkAll()
	 */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.page'));
	}

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		// set default values for last editor
		if (!isset($this->parameters['data']['lastEditorID'])) {
			$this->parameters['data']['lastEditorID'] = WCF::getUser()->userID;
			$this->parameters['data']['lastEditorName'] = WCF::getUser()->username;
		}

		// set default value for last edit time
		if (!isset($this->parameters['data']['lastEditTime'])) {
			$this->parameters['data']['lastEditTime'] = TIME_NOW;
		}

		// perform update
		parent::update();
	}

	/**
	 * @see	\wcf\data\ISortableAction::validateUpdatePosition()
	 */
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

	/**
	 * @see	\wcf\data\ISortableAction::updatePosition()
	 */
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

		// create revision
		$this->parameters['action'] = 'updatePosition';
		$this->createRevision();
	}
}
