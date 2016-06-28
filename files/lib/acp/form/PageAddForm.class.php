<?php
namespace cms\acp\form;

use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
use cms\data\stylesheet\StylesheetList;
use cms\util\PageUtil;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemEditor;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\page\menu\item\ViewablePageMenuItem;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\style\StyleHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

/**
 * Shows the page add form.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';

	/**
	 * alias of the created page
	 * @var	string
	 */
	public $alias = '';

	/**
	 * option to allow spiders to index the created page
	 * @var	integer
	 */
	public $allowIndexing = CMS_PAGES_DEFAULT_ALLOW_INDEXING;

	/**
	 * option to allow subscribing the created page
	 * @var	integer
	 */
	public $allowSubscribing = CMS_PAGES_DEFAULT_ALLOW_SUBSCRIBING;

	/**
	 * indication whether the created page is available during offline mode
	 * @var	integer
	 */
	public $availableDuringOfflineMode = CMS_PAGES_DEFAULT_OFFLINE;

	/**
	 * list of available styles
	 * @var	array<\wcf\data\style\Style>
	 */
	public $availableStyles = array();

	/**
	 * create menu item
	 * @var	integer
	 */
	public $createMenuItem = CMS_PAGES_DEFAULT_MENU_ITEM;

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
	 * indication whether this page is commentable
	 * @var	integer
	 */
	public $isCommentable = CMS_PAGES_DEFAULT_COMMENTS;

	/**
	 * id of the menu item that should be active when viewing the created
	 * page
	 * @var	integer
	 */
	public $menuItemID = 0;

	/**
	 * list of available menu items
	 * @var	array<\wcf\data\page\menu\item\PageMenuItem>
	 */
	public $menuItems = array();

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
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.page.canAddPage');

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
	public $sidebarOrientation = CMS_PAGES_DEFAULT_SIDEBAR;

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
	public $stylesheetIDs = array();
	
	public $availablePageTypes = array();
	
	public $pageObjectTypeID = 0;
	public $pageObjectType = null;
	
	public $specificFormParameters = array();
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		
		if (empty($_REQUEST['id']) && empty($_POST['pageObjectTypeID'])) {
			$this->pageObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.page');
			$this->pageObjectTypeID = $this->pageObjectType->objectTypeID;
		}

		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.page');

		// register i18n-values
		I18nHandler::getInstance()->register('title');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('metaDescription');
		I18nHandler::getInstance()->register('metaKeywords');

		// get available styles
		$this->availableStyles = StyleHandler::getInstance()->getStyles();
	}

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		I18nHandler::getInstance()->readValues();

		// general data
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
		if (isset($_POST['alias'])) $this->alias = StringUtil::trim($_POST['alias']);
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		$this->createMenuItem = (isset($_POST['createMenuItem'])) ? 1 : 0;

		// meta information
		if (I18nHandler::getInstance()->isPlainValue('metaDescription')) $this->metaDescription = StringUtil::trim(I18nHandler::getInstance()->getValue('metaDescription'));
		if (I18nHandler::getInstance()->isPlainValue('metaKeywords')) $this->metaKeywords = StringUtil::trim(I18nHandler::getInstance()->getValue('metaKeywords'));
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
		$this->isCommentable = (isset($_POST['isCommentable'])) ? 1 : 0;
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
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->pageObjectType) || $this->pageObjectType === null)
			throw new UserInputException('objectTypeID');

		// validate title
		if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			} else {
				throw new UserInputException('title', 'multilingual');
			}
		}

		// validate alias
		$this->validateAlias();

		// validate description
		if (!I18nHandler::getInstance()->validateValue('description', false, true)) {
			throw new UserInputException('description', 'multilingual');
		}

		// validate meta description
		if (!I18nHandler::getInstance()->validateValue('metaDescription', false, true)) {
			throw new UserInputException('metaDescription', 'multilingual');
		}

		// validate meta keywords
		if (!I18nHandler::getInstance()->validateValue('metaKeywords', false, true)) {
			throw new UserInputException('metaKeywords', 'multilingual');
		}

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
			$menuItem = new PageMenuItem($this->menuItemID);
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

		$this->stylesheetIDs = array();
		foreach ($stylesheetList as $stylesheet) {
			$this->stylesheetIDs[] = $stylesheet->stylesheetID;
		}

		// validate sidebar orientation
		if (!in_array($this->sidebarOrientation, array('left', 'right'))) {
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
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$data = array(
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
			'isCommentable' => $this->isCommentable,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'allowSubscribing' => $this->allowSubscribing,

			// display
			'styleID' => ($this->styleID) ?: null,

			// display settings
			'sidebarOrientation' => $this->sidebarOrientation,
			
			// page type
			'objectTypeID' => $this->pageObjectTypeID
		);

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
		$pageData = array_merge_recursive($specificPageData, array(
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		));

		$this->objectAction = new PageAction(array(), 'create', $pageData);
		$returnValues = $this->objectAction->executeAction();

		$pageEditor = new PageEditor($returnValues['returnValues']);
		$updateData = array();

		// save ACL
		ACLHandler::getInstance()->save($pageEditor->pageID, $this->objectTypeID);

		// save multilingual inputs
		if (!I18nHandler::getInstance()->isPlainValue('title')) {
			$updateData['title'] = 'cms.page.title'.$pageEditor->pageID;
			I18nHandler::getInstance()->save('title', $updateData['title'], 'cms.page');
		}
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			$updateData['description'] = 'cms.page.description'.$pageEditor->pageID;
			I18nHandler::getInstance()->save('description', $updateData['description'], 'cms.page');
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
			$updateData['metaDescription'] = 'cms.page.metaDescription'.$pageEditor->pageID;
			I18nHandler::getInstance()->save('metaDescription', $updateData['metaDescription'], 'cms.page');
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
			$updateData['metaKeywords'] = 'cms.page.metaKeywords'.$pageEditor->pageID;
			I18nHandler::getInstance()->save('metaKeywords', $updateData['metaKeywords'], 'cms.page');
		}

		// create menu item for page
		if ($this->createMenuItem) {
			// set menu item of parent page as parent menu item
			if ($pageEditor->getParentPage() !== null && $pageEditor->getParentPage()->menuItemID) {
				$parentMenuItem = new PageMenuItem($pageEditor->getParentPage()->menuItemID);
			}

			$menuItemData = array(
				'className' => 'cms\system\menu\page\CMSPageMenuItemProvider',
				'menuItemController' => 'cms\page\PagePage',
				'menuItemLink' => 'id='.$pageEditor->pageID,
				'menuPosition' => 'header',
				'packageID' => PACKAGE_ID,
				'parentMenuItem' => (isset($parentMenuItem) && $parentMenuItem->menuItemID) ? $parentMenuItem->menuItem : '',
				'showOrder' => 0
			);

			$menuItemAction = new PageMenuItemAction(array(), 'create', array('data' => $menuItemData));
			$menuItemReturnValues = $menuItemAction->executeAction();
			$menuItem = $menuItemReturnValues['returnValues'];

			// save multilingual title
			I18nHandler::getInstance()->register('menuItemTitle');
			I18nHandler::getInstance()->setValues('menuItemTitle', I18nHandler::getInstance()->getValues('title'));

			$menuItemData = array('menuItem' => 'wcf.page.menuItem'.$menuItem->menuItemID);
			I18nHandler::getInstance()->save('menuItemTitle', $menuItemData['menuItem'], 'wcf.page');

			$menuItemEditor = new PageMenuItemEditor($menuItem);
			$menuItemEditor->update($menuItemData);

			// save menu item with page
			$updateData['menuItemID'] = $menuItem->menuItemID;
		}

		// save new information
		$pageEditor->update($updateData);

		// create revision
		$objectAction = new PageAction(array($pageEditor->pageID), 'createRevision', array('action' => 'create'));
		$objectAction->executeAction();

		// update search index
		$objectAction = new PageAction(array($pageEditor->pageID), 'refreshSearchIndex');
		$objectAction->executeAction();
		
		$this->pageObjectType->getProcessor()->save($this);

		$this->saved();
		WCF::getTPL()->assign('success', true);

		// reset values
		$this->alias = $this->deactivationDate = $this->description = $this->metaDescription = $this->metaKeywords = $this->publicationDate = '';
		$this->enableDelayedDeactivation = $this->enableDelayedPublication = $this->invisible = $this->menuItemID = $this->parentID = $this->showOrder = $this->styleID = 0;
		$this->stylesheetIDs = $this->specificFormParameters = array();

		$this->allowIndexing = CMS_PAGES_DEFAULT_ALLOW_INDEXING;
		$this->allowSubscribing = CMS_PAGES_DEFAULT_ALLOW_SUBSCRIBING;
		$this->availableDuringOfflineMode = CMS_PAGES_DEFAULT_OFFLINE;
		$this->createMenuItem = CMS_PAGES_DEFAULT_MENU_ITEM;
		$this->isCommentable = CMS_PAGES_DEFAULT_COMMENTS;
		$this->sidebarOrientation = CMS_PAGES_DEFAULT_SIDEBAR;

		I18nHandler::getInstance()->reset();
		ACLHandler::getInstance()->disableAssignVariables();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
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
		$menuItemList = new PageMenuItemList();
		$menuItemList->getConditionBuilder()->add('page_menu_item.menuPosition = ?', array('header'));
		$menuItemList->sqlOrderBy = 'page_menu_item.parentMenuItem ASC, page_menu_item.showOrder ASC';
		$menuItemList->readObjects();

		foreach ($menuItemList as $menuItem) {
			if ($menuItem->parentMenuItem) {
				if (isset($this->menuItems[$menuItem->parentMenuItem])) {
					$this->menuItems[$menuItem->parentMenuItem]->addChild($menuItem);
				}
			} else {
				$this->menuItems[$menuItem->menuItem] = new ViewablePageMenuItem($menuItem);
			}
		}
		
		$this->specificFormParameters = $this->pageObjectType->getProcessor()->readData($this);
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		
		WCF::getTPL()->assign(array_merge_recursive($this->specificFormParameters, array(
			'action' => 'add',
			'availableStyles' => $this->availableStyles,
			'menuItems' => $this->menuItems,
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
			'isCommentable' => $this->isCommentable,
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
		)));
	}
}
