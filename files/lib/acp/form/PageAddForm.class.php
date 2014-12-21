<?php
namespace cms\acp\form;

use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
use cms\data\stylesheet\StylesheetList;
use cms\util\PageUtil;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemEditor;
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
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.page.canAddPage');

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
	 * indication whether a menu item should be created for the created
	 * page
	 * @var	integer
	 */
	public $menuItem = CMS_PAGES_DEFAULT_MENU_ITEM;

	/**
	 * id of the menu item that should be active when viewing the created
	 * page
	 * @var	integer
	 */
	public $menuItemID = null;

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
	public $parentID = null;

	/**
	 * publication date (ISO 8601)
	 * @var	string
	 */
	public $publicationDate = '';

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

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

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
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		if (I18nHandler::getInstance()->isPlainValue('metaDescription')) $this->metaDescription = StringUtil::trim(I18nHandler::getInstance()->getValue('metaDescription'));
		if (I18nHandler::getInstance()->isPlainValue('metaKeywords')) $this->metaKeywords = StringUtil::trim(I18nHandler::getInstance()->getValue('metaKeywords'));
		if (isset($_POST['alias'])) $this->alias = StringUtil::trim($_POST['alias']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['availableDuringOfflineMode'])) $this->availableDuringOfflineMode = intval($_POST['availableDuringOfflineMode']);
		else $this->availableDuringOfflineMode = 0;
		if (isset($_POST['invisible'])) $this->invisible = intval($_POST['invisible']);
		if (isset($_POST['menuItem'])) $this->menuItem = intval($_POST['menuItem']);
		else $this->menuItem = 0;
		$this->allowIndexing = (isset($_POST['allowIndexing'])) ? 1 : 0;
		if (isset($_POST['parentID'])) $this->parentID = intval($_POST['parentID']);
		if (isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);
		if (isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
		else $this->isCommentable = 0;
		$this->allowSubscribing = (isset($_POST['allowSubscribing'])) ? 1 : 0;
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['stylesheetIDs']) && is_array($_POST['stylesheetIDs'])) $this->stylesheetIDs = ArrayUtil::toIntegerArray($_POST['stylesheetIDs']);

		if (isset($_POST['enableDelayedPublication'])) $this->enableDelayedPublication = intval($_POST['enableDelayedPublication']);
		if (isset($_POST['publicationDate'])) $this->publicationDate = $_POST['publicationDate'];
		if (isset($_POST['enableDelayedDeactivation'])) $this->enableDelayedDeactivation = intval($_POST['enableDelayedDeactivation']);
		if (isset($_POST['deactivationDate'])) $this->deactivationDate = $_POST['deactivationDate'];
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		if (!I18nHandler::getInstance()->validateValue('title', true)) {
			throw new UserInputException('title', 'multilingual');
		}

		// validate alias
		$this->validateAlias();

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

		// validate style
		if ($this->styleID && !isset($this->availableStyles[$this->styleID])) {
			throw new UserInputException('styleID', 'notValid');
		}

		$page = new Page($this->parentID);
		if ($page === null) throw new UserInputException('parentID', 'invalid');

		// validate stylesheets
		$stylesheetList = new StylesheetList();
		$stylesheetList->setObjectIDs($this->stylesheetIDs);
		$stylesheetList->readObjects();

		$this->stylesheetIDs = array();
		foreach ($stylesheetList as $stylesheet) {
			$this->stylesheetIDs[] = $stylesheet->stylesheetID;
		}
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

	public function save() {
		parent::save();

		$data = array(
			'alias' => $this->alias,
			'description' => $this->description,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'invisible' => $this->invisible,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'showOrder' => $this->showOrder,
			'parentID' => ($this->parentID) ?  : null,
			'sidebarOrientation' => $this->sidebarOrientation,
			'allowIndexing' => $this->allowIndexing,
			'isCommentable' => $this->isCommentable,
			'allowSubscribing' => $this->allowSubscribing,
			'styleID' => ($this->styleID) ?: null
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

		$pageData = array(
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		);

		$this->objectAction = new PageAction(array(), 'create', $pageData);
		$returnValues = $this->objectAction->executeAction();

		$pageID = $returnValues['returnValues']->pageID;

		// save ACL
		ACLHandler::getInstance()->save($pageID, $this->objectTypeID);

		// update I18n
		$update = array();

		I18nHandler::getInstance()->save('title', 'cms.page.title' . $pageID, 'cms.page');
		$update['title'] = 'cms.page.title' . $pageID;

		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'cms.page.description' . $pageID, 'cms.page');
			$update['description'] = 'cms.page.description' . $pageID;
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
			I18nHandler::getInstance()->save('metaDescription', 'cms.page.metaDescription' . $pageID, 'cms.page');
			$update['metaDescription'] = 'cms.page.metaDescription' . $pageID;
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
			I18nHandler::getInstance()->save('metaKeywords', 'cms.page.metaKeywords' . $pageID, 'cms.page');
			$update['metaKeywords'] = 'cms.page.metaKeywords' . $pageID;
		}

		if ($this->menuItem) {
			if ($returnValues['returnValues']->getParentPage() !== null) {
				$parentPage = $returnValues['returnValues']->getParentPage();
				$parentItem = new PageMenuItem($parentPage->menuItemID);
			}

			$data = array(
				'className' => 'cms\system\menu\page\CMSPageMenuItemProvider',
				'menuItemController' => 'cms\page\PagePage',
				'menuItemLink' => 'id=' . $pageID,
				'menuPosition' => 'header',
				'packageID' => PACKAGE_ID,
				'options' => '',
				'permissions' => '',
				'parentMenuItem' => isset($parentItem) ? $parentItem->menuItem : '',
				'showOrder' => 0
			);

			$menuItemAction = new PageMenuItemAction(array(), 'create', array(
				'data' => $data
			));
			$itemReturnValues = $menuItemAction->executeAction();
			$menuItem = $itemReturnValues['returnValues'];

			I18nHandler::getInstance()->save('title', 'wcf.page.menuItem.' . $menuItem->menuItemID, 'wcf.page');
			$data['menuItem'] = 'wcf.page.menuItem.' . $menuItem->menuItemID;
			$editor = new PageMenuItemEditor($menuItem);
			$editor->update($data);

			$update['menuItemID'] = $menuItem->menuItemID ?  : null;
		}

		if (!empty($update)) {
			$editor = new PageEditor($returnValues['returnValues']);
			$editor->update($update);
		}

		// create revision
		$objectAction = new PageAction(array($pageID), 'createRevision', array(
			'action' => 'create'
		));
		$objectAction->executeAction();

		// update search index
		$objectAction = new PageAction(array($pageID), 'refreshSearchIndex');
		$objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);

		$this->description = $this->metaDescription = $this->metaKeywords = $this->robots = $this->alias = '';
		$this->sidebarOrientation = 'right';
		$this->deactivationDate = $this->enableDelayedDeactivation = $this->enableDelayedPublication = $this->invisible = $this->parentID = $this->publicationDate = $this->showOrder = $this->styleID = 0;
		$this->allowIndexing = $this->allowSubscribing = $this->menuItem = 1;
		$this->stylesheetIDs = array();

		I18nHandler::getInstance()->reset();
		ACLHandler::getInstance()->disableAssignVariables();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		if (isset($_REQUEST['id'])) $this->parentID = intval($_REQUEST['id']);

		// set default values
		if (empty($_POST)) {
			$dateTime = DateUtil::getDateTimeByTimestamp(TIME_NOW);
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->deactivationDate = $this->publicationDate = $dateTime->format('c');
		}

		$this->pageList = new PageNodeTree();
		$this->pageList = $this->pageList->getIterator();

		$this->stylesheetList = new StylesheetList();
		$this->stylesheetList->readObjects();
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		I18nHandler::getInstance()->assignVariables();
		ACLHandler::getInstance()->assignVariables($this->objectTypeID);

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'objectTypeID' => $this->objectTypeID,
			'invisible' => $this->invisible,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'allowIndexing' => $this->allowIndexing,
			'description' => $this->description,
			'alias' => $this->alias,
			'parentID' => $this->parentID,
			'showOrder' => $this->showOrder,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'menu' => $this->menuItem,
			'sidebarOrientation' => $this->sidebarOrientation,
			'pageList' => $this->pageList,
			'isCommentable' => $this->isCommentable,
			'allowSubscribing' => $this->allowSubscribing,
			'availableStyles' => $this->availableStyles,
			'styleID' => $this->styleID,
			'stylesheetIDs' => $this->stylesheetIDs,
			'stylesheetList' => $this->stylesheetList->getObjects(),
			'enableDelayedDeactivation' => $this->enableDelayedDeactivation,
			'enableDelayedPublication' => $this->enableDelayedPublication,
			'deactivationDate' => $this->deactivationDate,
			'publicationDate' => $this->publicationDate
		));
	}
}
