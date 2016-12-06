<?php
namespace cms\data\page;

use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\stylesheet\StylesheetList;
use cms\system\cache\builder\PageCacheBuilder;
use cms\system\content\type\ISearchableContentType;
use cms\system\menu\page\CMSPageMenuItemProvider;
use cms\util\PageUtil;
use wcf\data\menu\item\MenuItem;
use wcf\data\menu\item\MenuItemAction;
use wcf\data\menu\item\MenuItemEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\PackageCache;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\page\menu\item\ViewablePageMenuItem;
use wcf\data\page\PageAction as WCFPageAction;
use wcf\data\page\PageEditor as WCFPageEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\search\SearchIndexManager;
use wcf\system\style\StyleHandler;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Executes page-related actions.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageAction extends AbstractDatabaseObjectAction implements IClipboardAction, ISortableAction, IToggleAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\page\PageEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.cms.page.canAddPage');
	
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
	protected $resetCache = array('copy', 'create', 'delete', 'disable', 'enable', 'publish', 'setAsHome', 'toggle', 'update', 'updatePosition', 'frontendCreate');

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
		$i = 1;
		$alias = $data['alias'] . '-copy';
		do {
			$data['alias'] = $alias . $i;
			$i++;
		} 
		while (!PageUtil::isAvailableAlias($data['alias'], $object->parentID));

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

		// setting new IDs
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
		
		if (isset($this->parameters['data']['additionalData']) && is_array($this->parameters['data']['additionalData'])) {
			$this->parameters['data']['additionalData'] = serialize($this->parameters['data']['additionalData']);
		}

		$wcfPageData = array(
			'data' => array(
				'name' => (!empty($this->parameters['data']['title']) ? $this->parameters['data']['title'] : ''),
				'identifier' => 'de.codequake.cms.Page',
				'pageType' => 'system',
				'isDisabled' => (isset($this->parameters['data']['isDisabled']) && intval($this->parameters['data']['isDisabled']) == 1),
				'originIsSystem' => 1,
				'packageID' => PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms')->packageID,
				'applicationPackageID' => PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms')->packageID,
				'controller' => 'cms\\page\\PagePage',
				'requireObjectID' => 1,
				'lastUpdateTime' => $this->parameters['data']['creationTime']
			)
		);
		$wcfPageAction = new WCFPageAction(array(), 'create', $wcfPageData);
		$wcfPage = $wcfPageAction->executeAction()['returnValues'];

		$this->parameters['data']['wcfPageID'] = $wcfPage->pageID;

		// create page itself
		/** @var $page Page */
		$page = parent::create();
		$pageEditor = new PageEditor($page);

		// handle stylesheets
		if (isset($this->parameters['stylesheetIDs']) && !empty($this->parameters['stylesheetIDs'])) {
			$pageEditor->updateStylesheetIDs($this->parameters['stylesheetIDs']);
		}

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
		// get available languages
		$availableLanguages = LanguageFactory::getInstance()->getLanguages();
		
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $object) {
			$contentData = array();

			$contentList = new ContentList();
			$contentList->getConditionBuilder()->add('content.pageID = ?', array($object->pageID));
			$contentList->readObjects();

			foreach ($contentList as $content) {
				if ($content->getObjectType() === null)
					continue;
				
				$objectType = $content->getObjectType();
				
				$tmpContentData = $content->getData();
				
				$langItem = $tmpContentData['title'];
				$tmpContentData['title'] = array();
				foreach ($availableLanguages as $lang) {
					$tmpContentData['title'][$lang->countryCode] = $lang->get($langItem);
				}
				
				foreach ($objectType->getProcessor()->multilingualFields as $field) {
					if (isset($tmpContentData['contentData'][$field])) {
						$langItem = $tmpContentData['contentData'][$field];
						$tmpContentData['contentData'][$field] = array();
						foreach ($availableLanguages as $lang) {
							$tmpContentData['contentData'][$field][$lang->countryCode] = $lang->get($langItem);
						}
					}
				}
				
				$contentData[] = $tmpContentData;
			}
			
			$pageData = $object->getDecoratedObject()->getData();
			foreach ($pageData as $key => $element) {
				if ($key == 'title' || $key == 'description' || $key == 'metaDescription' || $key == 'metaKeywords') {
					$langItem = $pageData[$key];
					$pageData[$key] = array();
					foreach ($availableLanguages as $lang) {
						$pageData[$key][$lang->countryCode] = $lang->get($langItem);
					}
				}
			}
			
			call_user_func(array($this->className, 'createRevision'), array(
				'pageID' => $object->getObjectID(),
				'action' => (isset($this->parameters['action']) ? $this->parameters['action'] : 'create'),
				'userID' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				'time' => TIME_NOW,
				'data' => base64_encode(serialize($pageData)),
				'contentData' => base64_encode(serialize($contentData))
			));
		}
	}

	/**
	 * @see	\wcf\data\IDeleteAction::validateDelete()
	 */
	public function validateDelete() {
		parent::validateDelete();

		foreach ($this->objects as $pageEditor) {
			if (!$pageEditor->canDelete()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		$returnValues = parent::delete();

		$wcfPageIDs = $menuItemIDs = $pageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;

			if ($pageEditor->wcfPageID !== null) {
				$wcfPageIDs[] = $pageEditor->wcfPageID;
			}


			if ($pageEditor->menuItemID !== null) {
				$menuItemIDs[] = $pageEditor->menuItemID;
			}
		}

		if (!empty($wcfPageIDs)) {
			$wcfPageAction = new WCFPageAction(array($wcfPageIDs), 'delete');
			$wcfPageAction->executeAction();
		}

		if (!empty($menuItemIDs)) {
			$menuItemAction = new MenuItemAction(array($menuItemIDs), 'delete');
			$menuItemAction->executeAction();
		}

		// update search index
		if (!empty($pageIDs)) {
			SearchIndexManager::getInstance()->delete('de.codequake.cms.page', $pageIDs);
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

			$wcfPageEditor = new WCFPageEditor($pageEditor->getWCFPage());
			$wcfPageEditor->update(array('isDisabled' => 1));

			$menuItem = $pageEditor->getMenuItem();
			if ($menuItem !== null) {
				$menuItemEditor = new MenuItemEditor($pageEditor->getMenuItem());
				$menuItemEditor->update(array('isDisabled' => 1));
			}
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

			$wcfPageEditor = new WCFPageEditor($pageEditor->getWCFPage());
			$wcfPageEditor->update(array('isDisabled' => 0));

			$menuItem = $pageEditor->getMenuItem();
			if ($menuItem !== null) {
				$menuItemEditor = new MenuItemEditor($pageEditor->getMenuItem());
				$menuItemEditor->update(array('isDisabled' => 0));
			}
		}
	}
	
	/**
	 * Validates parameters to create a page in frontend
	 */
	 public function validateFrontendCreate() {
		//check permission
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) throw new AJAXException();
		//TODO I18n & other error handling
		if (!isset($this->parameters['data']['parentID'])) $this->parameters['data']['parentID'] = null;
		
		//validate alias
		if ($this->parameters['data']['alias'] == '') {
			$this->parameters['data']['alias'] = PageUtil::buildAlias($this->parameters['data']['title']);
		}
		if (!PageUtil::isValidAlias($this->parameters['data']['alias'])) {
			throw new UserInputException('alias', 'notValid');
		}
		if (!PageUtil::isAvailableAlias($this->parameters['data']['alias'], isset($this->parameters['data']['parentID']) ? $this->parameters['data']['parentID'] : null)) {
			throw new UserInputException('alias', 'notUnique');
		}
		
		//validate sidebarOrientation
		if (!in_array($this->parameters['data']['sidebarOrientation'], array('left', 'right'))) {
			// force default value if invalid sidebar orientation
			// specified
			$this->parameters['data']['sidebarOrientation'] = 'right';
		}
		
		// validate parent page
		if ($this->parameters['data']['parentID']) {
			$parentPage = PageCache::getInstance()->getPage($this->parameters['data']['parentID']);
			if ($parentPage === null) {
				$this->parameters['data']['parentID'] = null;
			}
		}
		
		// validate publication date
		if ($this->parameters['data']['enableDelayedPublication']) {
			$publicationDateTimestamp = @strtotime($this->parameters['data']['publicationDate']);
			if ($publicationDateTimestamp === false || $publicationDateTimestamp <= TIME_NOW) {
				throw new UserInputException('publicationDate', 'notValid');
			}
			// integer overflow
			if ($publicationDateTimestamp > 2147483647) {
				throw new UserInputException('publicationDate', 'notValid');
			}
		}
		
		// validate deactivation date
		if ($this->parameters['data']['enableDelayedDeactivation']) {
			$deactivationDateTimestamp = @strtotime($this->parameters['data']['deactivationDate']);
			if ($deactivationDateTimestamp === false || $deactivationDateTimestamp <= TIME_NOW) {
				throw new UserInputException('deactivationDate', 'notValid');
			}
			// integer overflow
			if ($deactivationDateTimestamp > 2147483647) {
				throw new UserInputException('deactivationDate', 'notValid');
			}
			// deactivation date needs to be after publication date
			if ($this->enableDelayedPublication && $deactivationDateTimestamp < $publicationDateTimestamp) {
				throw new UserInputException('deactivationDate', 'beforePublication');
			}
		}
		
		// validate menu item
		if ($this->parameters['data']['createMenuItem']) {
			$this->parameters['data']['menuItemID'] = 0;
		}
		if ($this->parameters['data']['menuItemID']) {
			$menuItem = new PageMenuItem($this->parameters['data']['menuItemID']);
			if (!$menuItem->menuItemID) {
				// silently ignore menu item, user shouldn't be
				// able to select this menu item in first place
				$this->parameters['data']['menuItemID'] = 0;
			}
		}
	 }
	 
	/**
	 * performs frontend create
	 */
	public function frontendCreate() {
		$data = $this->parameters['data'];
		// publication
		if ($data['enableDelayedPublication']) {
			$data['isPublished'] = 0;
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $data['publicationDate'], WCF::getUser()->getTimeZone());
			$data['publicationDate'] = $dateTime->getTimestamp();
		} else $data['publicationDate'] = 0;
		if ($data['enableDelayedDeactivation']) {
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $data['deactivationDate'], WCF::getUser()->getTimeZone());
			$data['deactivationDate'] = $dateTime->getTimestamp();
		}
		else $data['deactivationDate'] = 0;
		
		//get stylesheetIDs
		$stylesheetIDs = array();
		if (isset($data['stylesheetIDs'])) $stylesheetIDs = $data['stylesheetIDs'];
		
		//check foreigns
		if ($data['menuItemID'] == 0) $data['menuItemID'] = null;
		if ($data['parentID'] == 0) $data['parentID'] = null;
		if ($data['styleID'] == 0) $data['styleID'] = null;
		
		//unset useless parameters
		unset($data['enableDelayedPublication']);
		unset($data['enableDelayedDeactivation']);
		unset($data['createMenuItem']);
		unset($data['t']);
		unset($data['stylesheetIDs']);
		
		//unset bullshit
		unset($data['undefined']);
		
		//set params
		$this->parameters['stylesheetIDs'] = $stylesheetIDs;
		$this->parameters['data'] = $data;
		
		//finally create new page
		$page = $this->create();
		return array(
			'page' => $page,
			'link' => $page->getLink()
			);
	}
	
	/**
	 * Validates parameters to get the page add dialog.
	 */
	public function validateGetAddDialog() {
		//check permission
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) throw new AJAXException();
	}

	/**
	 * Returns the page add dialog.
	 */
	public function getAddDialog() {
		if (!empty($this->objectIDs)) {
			//pageID = 0 -> add
			if (reset($this->objectIDs) == 0) {
				$action = 'add';
				$page = null;
			}
			
			//valid object -> edit
			else if ($this->getSingleObject()) {
				$action = 'edit';
				$page = $this->getSingleObject();
			}
			
			//bullshit given
			else throw new AJAXException();
		}
		else {
			$action = 'add';
			$page = null;
		}
		
		//default date
		$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
		$dateTime->setTimezone(WCF::getUser()->getTimeZone());
		$defaultDate = $dateTime->format('c');
		
		//get initial data
		$alias = $page ? $page->alias : '';
		$allowIndexing = $page ? $page->allowIndexing : FIREBALL_PAGES_DEFAULT_ALLOW_INDEXING;
		$allowSubscribing = $page ? $page->allowSubscribing : FIREBALL_PAGES_DEFAULT_ALLOW_SUBSCRIBING;
		$availableDuringOfflineMode = $page ? $page->availableDuringOfflineMode : FIREBALL_PAGES_DEFAULT_OFFLINE;
		$createMenuItem = FIREBALL_PAGES_DEFAULT_MENU_ITEM;
		$deactivationDate = $page ? $page->deactivationDate : $defaultDate;
		$description = $page ? $page->description : '';
		$enableDelayedDeactivation = $page ? $page->enableDelayedDeactivation : 0;
		$enableDelayedPublication = $page ? $page->enableDelayedPublication : 0;
		$invisible = $page ? $page->invisible : 0;
		$isCommentable = $page ? $page->isCommentable : FIREBALL_PAGES_DEFAULT_COMMENTS;
		$menuItemID = $page ? $page->menuItemID : 0;
		$metaDescription = $page ? $page->metaDescription : '';
		$metaKeywords = $page ? $page->metaKeywords : '';
		$pageID = $page ? $page->pageID : 0;
		$publicationDate = $page ? $page->publicationDate : $defaultDate;
		$parentID = $page ? $page->parentID : 0;
		$showOrder = $page ? $page->showOrder : 0;
		$sidebarOrientation = $page ? $page->sidebarOrientation : FIREBALL_PAGES_DEFAULT_SIDEBAR;
		$styleID = $page ? $page->styleID : 0;
		$stylesheetIDs = $page ? $page->getStylesheetIDs() : array();
		$title = $page ? $page->title : '';
		
		//read data
		$stylesheetList = new StylesheetList();
		$stylesheetList->readObjects();
		$stylesheetList = $stylesheetList->getObjects();
		$availableStyles = StyleHandler::getInstance()->getStyles();
		$pageNodeTree = new PageNodeTree();
		$pageList = $pageNodeTree->getIterator();
		
		// load menu items
		$menuItemList = new PageMenuItemList();
		$menuItemList->getConditionBuilder()->add('page_menu_item.menuPosition = ?', array('header'));
		$menuItemList->sqlOrderBy = 'page_menu_item.parentMenuItem ASC, page_menu_item.showOrder ASC';
		$menuItemList->readObjects();
		foreach ($menuItemList as $menuItem) {
			if ($menuItem->parentMenuItem) {
				if (isset($menuItems[$menuItem->parentMenuItem])) {
					$menuItems[$menuItem->parentMenuItem]->addChild($menuItem);
				}
			} else {
				$menuItems[$menuItem->menuItem] = new ViewablePageMenuItem($menuItem);
			}
		}
		
		WCF::getTPL()->assign(array(
			'action' => $action,
			'alias' => $alias,
			'allowIndexing' => $allowIndexing,
			'allowSubscribing' => $allowSubscribing,
			'availableDuringOfflineMode' => $availableDuringOfflineMode,
			'createMenuItem' => $createMenuItem,
			'deactivationDate' => $deactivationDate,
			'description' => $description,
			'enableDelayedDeactivation' => $enableDelayedDeactivation,
			'enableDelayedPublication' => $enableDelayedPublication,
			'invisible' => $invisible,
			'isCommentable' => $isCommentable,
			'menuItemID' => $menuItemID,
			'metaDescription' => $metaDescription,
			'metaKeywords' => $metaKeywords,
			'publicationDate' => $publicationDate,
			'parentID' => $parentID,
			'showOrder' => $showOrder,
			'sidebarOrientation' => $sidebarOrientation,
			'styleID' => $styleID,
			'stylesheetIDs' => $stylesheetIDs,
			'title' => $title,
			
			'stylesheetList' => $stylesheetList,
			'availableStyles' => $availableStyles,
			'pageList' => $pageList,
			'action' => $action,
			'pageID' => $pageID,
			'menuItems' => $menuItems
		));

		return array(
			'pageID' => $pageID,
			'template' => WCF::getTPL()->fetch('pageAddDialog', 'cms')
		);
	}

	/**
	 * Validates parameters to get a rendered list of content types.
	 */
	public function validateGetContentTypes() {
		$this->readString('position');
		if (!in_array($this->parameters['position'], array('body', 'sidebar', 'both'))) {
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
			if ($this->parameters['position'] == 'both') array_push($categories[$type->category], $type);
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
	 * Validates permissions and parameters to set a page ad home.
	 */
	public function validateSetAsHome() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.page.canAddPage'
		));

		// validate 'objectIDs' parameter
		$page = $this->getSingleObject();

		if ($page->isHome) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * Sets a specific page as home.
	 */
	public function setAsHome() {
		$page = $this->getSingleObject();

		$page->setAsHome();

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
		if (empty($this->objects)) {
			$this->readObjects();
		}

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
		
		if (isset($this->parameters['data']['additionalData']) && is_array($this->parameters['data']['additionalData'])) {
			$this->parameters['data']['additionalData'] = serialize($this->parameters['data']['additionalData']);
		}

		// perform update
		parent::update();

		$pageIDs = $publishedPageIDs = array();
		foreach ($this->objects as $pageEditor) {
			$pageIDs[] = $pageEditor->pageID;

			// update stylesheets
			if (isset($this->parameters['stylesheetIDs'])) {
				$pageEditor->updateStylesheetIDs($this->parameters['stylesheetIDs']);
			}

			if (!$pageEditor->isPublished) {
				$publishedPageIDs[] = $pageEditor->pageID;
			}

			if ($pageEditor->menuItemID !== null && !empty($this->parameters['data']['title'])) {
				$menuItemEditor = new MenuItemEditor($pageEditor->getMenuItem());
				$menuItemEditor->update(array('title' => $this->parameters['data']['title']));
			}

			if ($pageEditor->wcfPageID !== null && !empty($this->parameters['data']['title'])) {
				$wcfPageEditor = new WCFPageEditor($pageEditor->getWCFPage());
				$wcfPageEditor->update(array(
					'name' => $this->parameters['data']['title'],
					'lastUpdateTime' => TIME_NOW
				));
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
	}

	public function validateGetTypeSpecificForm() {
		if (empty($this->parameters['objectTypeID']))
			throw new UserInputException('objectTypeID');
		
		$processor = ObjectTypeCache::getInstance()->getObjectType($this->parameters['objectTypeID'])->getProcessor();
		if (!$processor->isAvailableToAdd())
			throw new PermissionDeniedException();
	}

	public function getTypeSpecificForm() {
		$processor = ObjectTypeCache::getInstance()->getObjectType($this->parameters['objectTypeID'])->getProcessor();
		$template = $processor->getCompiledFormTemplate();
		
		if (!empty($this->parameters['pageID'])) {
			$page = PageCache::getInstance()->getPage($this->parameters['pageID']);
			if ($page === null)
				throw new UserInputException('pageID');
		}
		
		return array(
			'template' => $template
		);
	}
}
