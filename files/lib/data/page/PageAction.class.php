<?php
namespace cms\data\page;

use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\system\cache\builder\PageCacheBuilder;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use cms\system\content\type\ISearchableContentType;
use cms\system\revision\PageRevisionHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\AJAXException;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\WCF;

/**
 * Executes page-related actions.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
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
	protected $resetCache = array('copy', 'create', 'delete', 'disable', 'enable', 'publish', 'restoreRevision', 'setAsHome', 'toggle', 'update', 'updatePosition');

	/**
	 * Validates parameters to copy a page.
	 */
	public function validateCopy() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Copies a specific page.
	 */
	public function copy() {
		$object = $this->getSingleObject();
		$data = $object->getDecoratedObject()->getData();

		// remove unique or irrelevant properties
		unset($data['pageID']);
		unset($data['isHome']);
		unset($data['clicks']);

		// ensure unique aliases
		// @todo	multiple copies of a page have the same alias
		$data['alias'] .= '-copy';

		// perform creation of copy
		$this->parameters['data'] = $data;
		$page = $this->create();
		$pageID = $page->pageID;

		// copy contents
		$contents = $object->getContents();
		$tmp = array();

		foreach (array('body', 'sidebar') as $position) {
			foreach ($contents[$position] as $content) {
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
		}

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

		// trigger publication
		if (!$page->isDisabled && $page->isPublished) {
			$action = new PageAction(array($pageEditor), 'triggerPublication');
			$action->executeAction();
		}

		return $page;
	}

	/**
	 * Creates a new revision for pages.
	 */
	protected function createRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $object) {
			call_user_func(array($this->className, 'createRevision'), array(
				'pageID' => $object->getObjectID(),
				'action' => (isset($this->parameters['action']) ? $this->parameters['action'] : 'create'),
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
		$returnValues = parent::delete();

		$menuItemIDs = $pageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;
			if ($pageEditor->menuItemID) $menuItemIDs[] = $pageEditor->menuItemID;
		}

		// update search index
		if (!empty($pageIDs)) {
			SearchIndexManager::getInstance()->delete('de.codequake.cms.page', $pageIDs);
		}

		// delete related menu items
		if (!empty($menuItemIDs)) {
			$objectAction = new PageMenuItemAction($menuItemIDs, 'delete');
			$objectAction->executeAction();
		}

		// check if first page
		PageCacheBuilder::getInstance()->reset();
		if (PageCache::getInstance()->getHomePage() === null) {
			$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
			$page = reset($pages);
			if ($page != null) {
				$editor = new PageEditor($page);
				$editor->setAsHome();
			}
		}

		return $returnValues;
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
		$this->readString('position');
		if (!in_array($this->parameters['position'], array('body', 'sidebar'))) {
			throw new UserInputException('position');
		}

		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Returns a rendered list of content types.
	 */
	public function getContentTypes() {
		$page = $this->getSingleObject();

		$types = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');
		foreach ($types as $key => $type) {
			if (!$type->getProcessor()->isAvailableToAdd()) {
				unset($types[$key]);
			}
		}

		$categories = array();
		foreach ($types as $type) {
			$categories[$type->category] = array();
		}

		foreach ($types as $type) {
			if ($this->parameters['position'] == 'body' && $type->allowcontent) array_push($categories[$type->category], $type);
			if ($this->parameters['position'] == 'sidebar' && $type->allowsidebar) array_push($categories[$type->category], $type);
		}

		WCF::getTPL()->assign(array(
			'contentTypes' => $categories,
			'page' => $page,
			'parentID' => isset($this->parameters['parentID']) ? intval($this->parameters['parentID']) : null,
			'position' => $this->parameters['position']
		));

		return array(
			'pageID' => $page->pageID,
			'parentID' => isset($this->parameters['parentID']) ? intval($this->parameters['parentID']) : null,
			'position' => $this->parameters['position'],
			'template' => WCF::getTPL()->fetch('contentTypeList', 'cms')
		);
	}

	/**
	 * Validates permissions and parameters for page revision list.
	 */
	public function validateGetRevisions() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Returns a rendered list of page revisions.
	 */
	public function getRevisions() {
		$page = $this->getSingleObject();
		$revisions = $page->getRevisions();

		WCF::getTPL()->assign(array(
			'pageID' => $page->pageID,
			'revisions' => $revisions
		));

		return array(
			'pageID' => $page->pageID,
			'revisions' => $revisions,
			'template' => WCF::getTPL()->fetch('pageRevisionList', 'cms')
		);
	}

	/**
	 * Publishes pages.
	 */
	public function publish() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$pageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;
			$pageEditor->update(array(
				'isPublished' => 1,
				'publicationDate' => 0
			));
		}

		// trigger publication
		if (!empty($pageIDs)) {
			$action = new PageAction($pageIDs, 'triggerPublication');
			$action->executeAction();
		}
	}

	/**
	 * Refreshes the search index
	 */
	public function refreshSearchIndex() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		$pageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;
		}

		if (!isset($this->parameters['isBulkProcessing']) || !$this->parameters['isBulkProcessing']) SearchIndexManager::getInstance()->delete('de.codequake.cms.page', $pageIDs);

		foreach ($this->objects as $pageEditor) {
			$contents = $pageEditor->getDecoratedObject()->getContents();

			$metaData = array();
			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				$metaData[$language->languageID] = '';
			}

			foreach (array('body', 'sidebar') as $position) {
				foreach ($contents[$position] as $content) {
					if ($content->getObjectType()->getProcessor() instanceof ISearchableContentType) {
						$searchIndexData = $content->getObjectType()->getProcessor()->getSearchableData($content->getDecoratedObject());

						foreach ($searchIndexData as $languageID => $data) {
							if (!empty($metaData[$languageID])) $metaData[$languageID] .= "\n";
							$metaData[$languageID] .= $data;
						}
					}
				}
			}

			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				SearchIndexManager::getInstance()->add(
					'de.codequake.cms.page',
					$pageEditor->pageID,
					$language->get($pageEditor->description),
					$language->get($pageEditor->title),
					$pageEditor->creationTime,
					$pageEditor->authorID,
					$pageEditor->authorName,
					$language->languageID,
					isset($metaData[$language->languageID])? $metaData[$language->languageID]: ''
				);
			}
		}

	}

	/**
	 * Triggers the publication of pages. You may listen for this action to
	 * execute certain code once the relevant pages are publicly available.
	 */
	public function triggerPublication() { /* nothing */ }

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
		if (!$this->pageEditor->pageID) {
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

		$pageIDs = $publishedPageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;

			if (!$pageEditor->isPublished) {
				$publishedPageIDs[] = $pageEditor->pageID;
			}
		}

		// delete subscriptions if subscribing isn't allowed anymore
		if (isset($this->parameters['data']['allowSubscribing']) && !$this->parameters['data']['allowSubscribing']) {
			UserObjectWatchHandler::getInstance()->deleteObjects('de.codequake.cms.page', $pageIDs);
		}

		// trigger new publications
		if (isset($this->parameters['data']['isPublished']) && $this->parameters['data']['isPublished'] == 1 && !empty($publishedPageIDs)) {
			$action = new PageAction($publishedPageIDs, 'triggerPublication');
			$action->executeAction();
		}
	}

	/**
	 * @see	\wcf\data\ISortableAction::validateUpdatePosition()
	 */
	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions($this->permissionsUpdate);

		if (!isset($this->parameters['data']['structure']) || !is_array($this->parameters['data']['structure'])) {
			throw new UserInputException('structure');
		}

		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		foreach ($this->parameters['data']['structure'] as $parentID => $pageIDs) {
			if ($parentID) {
				// validate parent page
				if (!isset($pages[$parentID])) {
					throw new UserInputException('structure');
				}

				$this->objects[$parentID] = new PageEditor($pages[$parentID]);
			}

			$aliases = array();
			foreach ($pageIDs as $pageID) {
				// validate page
				if (!isset($pages[$pageID])) {
					throw new UserInputException('structure');
				}

				// validate alias
				if (in_array($pages[$pageID]->alias, $aliases)) {
					throw new UserInputException('structure');
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
					'parentID' => ($parentID != 0) ? $this->objects[$parentID]->pageID : null,
					'showOrder' => $position++
				));
			}
		}

		WCF::getDB()->commitTransaction();

		// create revision
		$this->parameters['action'] = 'updatePosition';
		$this->createRevision();
	}
}
