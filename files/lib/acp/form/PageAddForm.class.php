<?php
namespace cms\acp\form;

use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
use cms\data\stylesheet\StylesheetList;
use cms\util\PageUtil;
use wcf\acp\form\AbstractAcpForm;
use wcf\data\menu\item\MenuItemAction;
use wcf\data\menu\MenuCache;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\PackageCache;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\language\I18nValue;
use wcf\system\language\LanguageFactory;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Shows the page add form.
 * 
 * @author	Jens Krumsieck, Florian Frantzen, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageAddForm extends AbstractAcpForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.add';

	/**
	 * alias of the created page
	 * @var	string
	 */
	public $alias = '';

	/**
	 * option to allow spiders to index the created page
	 * @var	integer
	 */
	public $allowIndexing = FIREBALL_PAGES_DEFAULT_ALLOW_INDEXING;

	/**
	 * option to allow subscribing the created page
	 * @var	integer
	 */
	public $allowSubscribing = FIREBALL_PAGES_DEFAULT_ALLOW_SUBSCRIBING;

	/**
	 * indication whether the created page is available during offline mode
	 * @var	integer
	 */
	public $availableDuringOfflineMode = FIREBALL_PAGES_DEFAULT_OFFLINE;

	/**
	 * list of available styles
	 * @var	array<\wcf\data\style\Style>
	 */
	public $availableStyles = [];

	/**
	 * create menu item
	 * @var	integer
	 */
	public $createMenuItem = FIREBALL_PAGES_DEFAULT_MENU_ITEM;

	/**
	 * deactivation date (ISO 8601)
	 * @var	string
	 */
	public $deactivationDate = '';

	/**
	 * description of the created page
	 * @var	string
	 */
	public $description = '';

	/**
	 * enables a delayed deactivation of this page
	 * @var	integer
	 */
	public $enableDelayedDeactivation = 0;

	/**
	 * enables a delayed publication of this page
	 * @var	integer
	 */
	public $enableDelayedPublication = 0;

	/**
	 * indication whether the created page is invisible
	 * @var	integer
	 */
	public $invisible = 0;

	/**
	 * id of the menu item that should be active when viewing the created
	 * page
	 * @var	integer
	 */
	public $menuItemID = 0;
	
	/**
	 * list of available menu item nodes
	 * @var	\wcf\data\menu\item\MenuItemNode[]
	 */
	public $menuItemNodeList = [];

	/**
	 * meta description of the created page
	 * @var	string
	 */
	public $metaDescription = '';

	/**
	 * meta keywords of the created page
	 * @var	string
	 */
	public $metaKeywords = '';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.page.canAddPage'];

	/**
	 * object type id of the acl
	 * @var	integer
	 */
	public $objectTypeID = 0;

	/**
	 * list of all pages the created page can be assigned to
	 * @var	\RecursiveIteratorIterator
	 */
	public $pageList = null;

	/**
	 * id of the page the created page is assigned to
	 * @var	integer
	 */
	public $parentID = 0;

	/**
	 * publication date (ISO 8601)
	 * @var	string
	 */
	public $publicationDate = '';

	/**
	 * page title
	 * @var	string
	 */
	public $title = '';

	/**
	 * show order
	 * @var	integer
	 */
	public $showOrder = 0;

	/**
	 * orientation of the sidebar ('left' or 'right')
	 * @var	string
	 */
	public $sidebarOrientation = FIREBALL_PAGES_DEFAULT_SIDEBAR;

	/**
	 * style id
	 * @var	integer
	 */
	public $styleID = 0;

	/**
	 * list of all stylesheets
	 * @var	\cms\data\stylesheet\StylesheetList
	 */
	public $stylesheetList = null;

	/**
	 * stylesheet ids
	 * @var	array<integer>
	 */
	public $stylesheetIDs = [];
	
	public $availablePageTypes = [];
	
	public $pageObjectTypeID = 0;
	public $pageObjectType = null;
	
	public $specificFormParameters = [];
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		
		if (empty($_REQUEST['id']) && empty($_POST['pageObjectTypeID'])) {
			$this->pageObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.page');
			$this->pageObjectTypeID = $this->pageObjectType->objectTypeID;
		}

		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.page');

		$packageID = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms');
		
		$i18nTitle = new I18nValue('title');
		$i18nTitle->setLanguageItem('cms.page.title', 'cms.page', $packageID);
		$this->registerI18nValue($i18nTitle);
		
		$i18nDescription = new I18nValue('description');
		$i18nDescription->setLanguageItem('cms.page.description', 'cms.page', $packageID);
		$i18nDescription->setFlags(I18nValue::ALLOW_EMPTY);
		$this->registerI18nValue($i18nDescription);
		
		$i18nMetaDescription = new I18nValue('metaDescription');
		$i18nMetaDescription->setLanguageItem('cms.page.metaDescription', 'cms.page', $packageID);
		$i18nMetaDescription->setFlags(I18nValue::ALLOW_EMPTY);
		$this->registerI18nValue($i18nMetaDescription);
		
		$i18nMetaKeywords = new I18nValue('metaKeywords');
		$i18nMetaKeywords->setLanguageItem('cms.page.metaKeywords', 'cms.page', $packageID);
		$i18nMetaKeywords->setFlags(I18nValue::ALLOW_EMPTY);
		$this->registerI18nValue($i18nMetaKeywords);
		
		// get available styles
		$this->availableStyles = StyleHandler::getInstance()->getStyles();
	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		// general data
		if (isset($_POST['alias'])) $this->alias = StringUtil::trim($_POST['alias']);
		$this->createMenuItem = (isset($_POST['createMenuItem'])) ? 1 : 0;

		// meta information
		$this->allowIndexing = (isset($_POST['allowIndexing'])) ? 1 : 0;

		// position
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['invisible'])) $this->invisible = intval($_POST['invisible']);
		
		// page type
		if (isset($_POST['pageObjectTypeID'])) $this->pageObjectTypeID = intval($_POST['pageObjectTypeID']);
		if (empty($this->pageObjectTypeID))
			$this->pageObjectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.page');
		$this->pageObjectType = ObjectTypeCache::getInstance()->getObjectType($this->pageObjectTypeID);
		
		// publication
		if (isset($_POST['enableDelayedPublication'])) $this->enableDelayedPublication = intval($_POST['enableDelayedPublication']);
		if (isset($_POST['publicationDate'])) $this->publicationDate = $_POST['publicationDate'];
		if (isset($_POST['enableDelayedDeactivation'])) $this->enableDelayedDeactivation = intval($_POST['enableDelayedDeactivation']);
		if (isset($_POST['deactivationDate'])) $this->deactivationDate = $_POST['deactivationDate'];

		// settings
		if (isset($_POST['menuItemID'])) $this->menuItemID = intval($_POST['menuItemID']);
		$this->availableDuringOfflineMode = (isset($_POST['availableDuringOfflineMode'])) ? 1 : 0;
		$this->allowSubscribing = (isset($_POST['allowSubscribing'])) ? 1 : 0;

		// display
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['stylesheetIDs']) && is_array($_POST['stylesheetIDs'])) $this->stylesheetIDs = ArrayUtil::toIntegerArray($_POST['stylesheetIDs']);

		// display settings
		if (isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);
		
		$this->specificFormParameters = $this->pageObjectType->getProcessor()->readFormParameters($this);
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->pageObjectType) || $this->pageObjectType === null)
			throw new UserInputException('objectTypeID');

		// validate alias
		$this->validateAlias();

		// validate parent page
		if ($this->parentID) {
			$parentPage = PageCache::getInstance()->getPage($this->parentID);
			if ($parentPage === null) {
				$this->parentID = 0;
			}
		}

		// validate publication date
		if ($this->enableDelayedPublication) {
			$publicationDateTimestamp = @strtotime($this->publicationDate);
			if ($publicationDateTimestamp === false || $publicationDateTimestamp <= TIME_NOW) {
				throw new UserInputException('publicationDate', 'notValid');
			}

			// integer overflow
			if ($publicationDateTimestamp > 2147483647) {
				throw new UserInputException('publicationDate', 'notValid');
			}
		}

		// validate deactivation date
		if ($this->enableDelayedDeactivation) {
			$deactivationDateTimestamp = @strtotime($this->deactivationDate);
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
		if ($this->createMenuItem) {
			$this->menuItemID = 0;
		}
		if ($this->menuItemID) {
			$menuItem = new MenuItem($this->menuItemID);
			if (!$menuItem->menuItemID) {
				// silently ignore menu item, user shouldn't be
				// able to select this menu item in first place
				$this->menuItemID = 0;
			}
		}

		// validate style
		if ($this->styleID && !isset($this->availableStyles[$this->styleID])) {
			throw new UserInputException('styleID', 'notValid');
		}

		// validate stylesheets
		$stylesheetList = new StylesheetList();
		$stylesheetList->setObjectIDs($this->stylesheetIDs);
		$stylesheetList->readObjects();

		$this->stylesheetIDs = [];
		foreach ($stylesheetList as $stylesheet) {
			$this->stylesheetIDs[] = $stylesheet->stylesheetID;
		}

		// validate sidebar orientation
		if (!in_array($this->sidebarOrientation, ['left', 'right'])) {
			// force default value if invalid sidebar orientation
			// specified
			$this->sidebarOrientation = 'right';
		}
		
		$this->pageObjectType->getProcessor()->validate($this);
	}

	/**
	 * Validates the alias.
	 */
	protected function validateAlias() {
		// build alias automatically
		if (empty($this->alias)) {
			$titles = I18nHandler::getInstance()->getValues('title');

			// prefer english aliases, otherwise use default language
			$language = LanguageFactory::getInstance()->getLanguageByCode('en');
			if ($language === null) {
				$language = LanguageFactory::getInstance()->getLanguage(LanguageFactory::getInstance()->getDefaultLanguageID());
			}

			$this->alias = PageUtil::buildAlias($titles[$language->languageID]);
		}

		if (!PageUtil::isValidAlias($this->alias)) {
			throw new UserInputException('alias', 'notValid');
		}
		if (!PageUtil::isAvailableAlias($this->alias, ($this->parentID) ?: null)) {
			throw new UserInputException('alias', 'notUnique');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$data = [
			// general data
			'title' => $this->title,
			'alias' => $this->alias,
			'description' => $this->description,

			// meta information
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'allowIndexing' => $this->allowIndexing,

			// position
			'parentID' => ($this->parentID) ?: null,
			'showOrder' => $this->showOrder,
			'invisible' => $this->invisible,

			// settings
			'menuItemID' => ($this->menuItemID) ?: null,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'allowSubscribing' => $this->allowSubscribing,

			// display
			'styleID' => ($this->styleID) ?: null,

			// display settings
			'sidebarOrientation' => $this->sidebarOrientation,
			
			// page type
			'objectTypeID' => $this->pageObjectTypeID
		];

		// publication
		if ($this->enableDelayedPublication) {
			$data['isPublished'] = 0;
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->publicationDate, WCF::getUser()->getTimeZone());
			$data['publicationDate'] = $dateTime->getTimestamp();
		}
		if ($this->enableDelayedDeactivation) {
			$dateTime = \DateTime::createFromFormat('Y-m-d H:i', $this->deactivationDate, WCF::getUser()->getTimeZone());
			$data['deactivationDate'] = $dateTime->getTimestamp();
		}
		
		$specificPageData =  $this->pageObjectType->getProcessor()->getSaveArray();
		$pageData = array_merge_recursive($specificPageData, [
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		]);

		$this->objectAction = new PageAction([], 'create', $pageData);
		$returnValues = $this->objectAction->executeAction();
		$page = $returnValues['returnValues'];

		$pageEditor = new PageEditor($returnValues['returnValues']);
		$updateData = [];

		// save ACL
		ACLHandler::getInstance()->save($page->pageID, $this->objectTypeID);
		
		$this->saveI18n($page, PageEditor::class);
		
		$this->createMenuItem($page->pageID, !empty($updateData['title']) ? $updateData['title'] : $this->title);

		// save new information
		$updateAction = new PageAction([$page], 'update', ['data' => $updateData]);
		$updateAction->executeAction();

		// create revision
		$objectAction = new PageAction([$pageEditor->pageID], 'createRevision', ['action' => 'create']);
		$objectAction->executeAction();

		// update search index
		$objectAction = new PageAction([$pageEditor->pageID], 'refreshSearchIndex');
		$objectAction->executeAction();
		
		$this->pageObjectType->getProcessor()->save($this);
		
		$this->reset();
	}
	
	/**
	 * @inheritDoc
	 */
	public function reset() {
		parent::reset();
		
		// reset values
		$this->alias = $this->deactivationDate = $this->publicationDate = '';
		$this->enableDelayedDeactivation = $this->enableDelayedPublication = $this->invisible = $this->menuItemID = $this->parentID = $this->showOrder = $this->styleID = 0;
		$this->stylesheetIDs = $this->specificFormParameters = [];
		
		$this->allowIndexing = FIREBALL_PAGES_DEFAULT_ALLOW_INDEXING;
		$this->allowSubscribing = FIREBALL_PAGES_DEFAULT_ALLOW_SUBSCRIBING;
		$this->availableDuringOfflineMode = FIREBALL_PAGES_DEFAULT_OFFLINE;
		$this->createMenuItem = FIREBALL_PAGES_DEFAULT_MENU_ITEM;
		$this->sidebarOrientation = FIREBALL_PAGES_DEFAULT_SIDEBAR;
		
		ACLHandler::getInstance()->disableAssignVariables();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// set default values
		if (empty($_POST)) {
			$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->deactivationDate = $this->publicationDate = $dateTime->format('c');
		}

		$pageNodeTree = new PageNodeTree();
		$this->pageList = $pageNodeTree->getIterator();

		$this->stylesheetList = new StylesheetList();
		$this->stylesheetList->readObjects();
		
		$this->availablePageTypes = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.page.type');
		foreach ($this->availablePageTypes as $key => $type) {
			if (!$type->getProcessor()->isAvailableToAdd()) {
				unset($this->availablePageTypes[$key]);
			}
		}
		
		// load menu items
		$mainMenu = MenuCache::getInstance()->getMainMenu();
		$this->menuItemNodeList = $mainMenu->getMenuItemNodeList();
		
		$this->specificFormParameters = $this->pageObjectType->getProcessor()->readData($this);
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		
		WCF::getTPL()->assign(array_merge_recursive($this->specificFormParameters, [
			'action' => 'add',
			'availableStyles' => $this->availableStyles,
			'menuItemNodeList' => $this->menuItemNodeList,
			'objectTypeID' => $this->objectTypeID,
			'pageList' => $this->pageList,
			'stylesheetList' => $this->stylesheetList->getObjects(),

			// general data
			'title' => $this->title,
			'alias' => $this->alias,
			'description' => $this->description,
			'createMenuItem' => $this->createMenuItem,

			// meta information
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'allowIndexing' => $this->allowIndexing,

			// position
			'parentID' => $this->parentID,
			'showOrder' => $this->showOrder,
			'invisible' => $this->invisible,

			// publication
			'enableDelayedDeactivation' => $this->enableDelayedDeactivation,
			'publicationDate' => $this->publicationDate,
			'enableDelayedPublication' => $this->enableDelayedPublication,
			'deactivationDate' => $this->deactivationDate,

			// settings
			'menuItemID' => $this->menuItemID,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'allowSubscribing' => $this->allowSubscribing,

			// display
			'styleID' => $this->styleID,
			'stylesheetIDs' => $this->stylesheetIDs,

			// display settings
			'sidebarOrientation' => $this->sidebarOrientation,
			
			// page type
			'availablePageTypes' => $this->availablePageTypes,
			'pageObjectTypeID' => $this->pageObjectTypeID,
			'pageForm' => $this->pageObjectType->getProcessor()->getCompiledFormTemplate($this->specificFormParameters)
		]));
	}
	
	/**
	 * Creates the menu item for this page
	 * @param integer $pageID
	 * @param string  $title
	 */
	public function createMenuItem($pageID, $title) {
		$page = new Page($pageID);
		
		// create menu item for page
		if ($this->createMenuItem) {
			// set menu item of parent page as parent menu item
			$parents = $page->getParentPages();
			$parentMenuItemID = null;
			foreach ($parents as $parent) {
				if ($parent->menuItemID) {
					$parentMenuItemID = $parent->menuItemID;
					break;
				}
			}

			$menuItemData = [
				'menuID' => MenuCache::getInstance()->getMainMenu()->menuID,
				'parentItemID' => $parentMenuItemID,
				'identifier' => 'de.codequake.cms.Page' . $page->pageID,
				'title' => $title,
				'pageID' => $page->wcfPageID,
				'originIsSystem' => 0,
				'pageObjectID' => $page->pageID,
				'isDisabled' => $this->invisible,
				'packageID' => PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms')->packageID
			];

			$menuItemAction = new MenuItemAction([], 'create', ['data' => $menuItemData]);
			$menuItemReturnValues = $menuItemAction->executeAction();
			$menuItem = $menuItemReturnValues['returnValues'];

			// save menu item with page
			$updateData['menuItemID'] = $menuItem->menuItemID;
		}
	}
}
