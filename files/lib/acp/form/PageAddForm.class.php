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

	public $objectTypeID = 0;

	public $pageID = 0;

	public $action = 'add';

	public $title = '';

	public $alias = '';

	public $description = '';

	public $metaDescription = '';

	public $metaKeywords = '';

	public $invisible = 0;

	public $availableDuringOfflineMode = CMS_PAGES_DEFAULT_OFFLINE;

	public $robots = CMS_PAGES_DEFAULT_ROBOTS;

	public $showSidebar = CMS_PAGES_DEFAULT_GLOBAL_SIDEBAR;

	public $sidebarOrientation = CMS_PAGES_DEFAULT_SIDEBAR;

	public $showOrder = 0;

	public $parentID = null;

	public $menuItem = CMS_PAGES_DEFAULT_MENU_ITEM;

	public $menuItemID = null;

	public $pageList = null;

	public $stylesheets = array();

	public $stylesheetList = null;

	public $isCommentable = CMS_PAGES_DEFAULT_COMMENTS;

	/**
	 * option to allow subscribing the created page
	 * @var	integer
	 */
	public $allowSubscribing = CMS_PAGES_DEFAULT_ALLOW_SUBSCRIBING;

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
	 * deactivation date (ISO 8601)
	 * @var	string
	 */
	public $deactivationDate = '';

	/**
	 * publication date (ISO 8601)
	 * @var	string
	 */
	public $publicationDate = '';

	/**
	 * list of available styles
	 * @var	array<\wcf\data\style\Style>
	 */
	public $availableStyles = array();

	/**
	 * style id
	 * @var	integer
	 */
	public $styleID = 0;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		I18nHandler::getInstance()->register('title');
		I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('metaDescription');
		I18nHandler::getInstance()->register('metaKeywords');

		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.page');

		// get available styles
		$this->availableStyles = StyleHandler::getInstance()->getStyles();
	}

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
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
		if (isset($_POST['robots'])) $this->robots = StringUtil::trim($_POST['robots']);
		if (isset($_POST['parentID'])) $this->parentID = intval($_POST['parentID']);
		if (isset($_POST['showSidebar'])) $this->showSidebar = intval($_POST['showSidebar']);
		else $this->showSidebar = 0;
		if (isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);
		if (isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
		else $this->isCommentable = 0;
		$this->allowSubscribing = (isset($_POST['allowSubscribing'])) ? 1 : 0;
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['stylesheets']) && is_array($_POST['stylesheets'])) $this->stylesheets = ArrayUtil::toIntegerArray($_POST['stylesheets']);

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

		if (!I18nHandler::getInstance()->validateValue('title')) {
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
			'title' => $this->title,
			'description' => $this->description,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'invisible' => $this->invisible,
			'availableDuringOfflineMode' => $this->availableDuringOfflineMode,
			'showOrder' => $this->showOrder,
			'parentID' => ($this->parentID) ?  : null,
			'showSidebar' => $this->showSidebar,
			'sidebarOrientation' => $this->sidebarOrientation,
			'robots' => $this->robots,
			'isCommentable' => $this->isCommentable,
			'allowSubscribing' => $this->allowSubscribing,
			'styleID' => ($this->styleID) ?: null,
			'stylesheets' => serialize($this->stylesheets)
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

		$objectAction = new PageAction(array(), 'create', array(
			'data' => $data
		));
		$objectAction->executeAction();

		$returnValues = $objectAction->getReturnValues();
		$pageID = $returnValues['returnValues']->pageID;

		// save ACL
		ACLHandler::getInstance()->save($pageID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();
		// update I18n
		$update = array();

		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.page.title' . $pageID, 'cms.page');
			$update['title'] = 'cms.page.title' . $pageID;
		}
		if (! I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'cms.page.description' . $pageID, 'cms.page');
			$update['description'] = 'cms.page.description' . $pageID;
		}
		if (! I18nHandler::getInstance()->isPlainValue('metaDescription')) {
			I18nHandler::getInstance()->save('metaDescription', 'cms.page.metaDescription' . $pageID, 'cms.page');
			$update['metaDescription'] = 'cms.page.metaDescription' . $pageID;
		}
		if (! I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
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

		if (! empty($update)) {
			$editor = new PageEditor($returnValues['returnValues']);
			$editor->update($update);
		}

		//create revision
		$objectAction = new PageAction(array(
			$returnValues['returnValues']->pageID
		), 'createRevision', array(
			'action' => 'create'
		));
		$objectAction->executeAction();

		//update search index
		$objectAction = new PageAction(array($returnValues['returnValues']->pageID), 'refreshSearchIndex');
		$objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);
		$this->title = $this->description = $this->metaDescription = $this->metaKeywords = $this->robots = $this->alias = '';
		$this->sidebarOrientation = 'right';
		$this->deactivationDate = $this->enableDelayedDeactivation = $this->enableDelayedPublication = $this->invisible = $this->parentID = $this->publicationDate = $this->showOrder = $this->showSidebar = $this->styleID = 0;
		$this->allowSubscribing = $this->menuItem = 1;
		I18nHandler::getInstance()->reset();
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
			'robots' => $this->robots,
			'description' => $this->description,
			'alias' => $this->alias,
			'parentID' => $this->parentID,
			'showOrder' => $this->showOrder,
			'metaDescription' => $this->metaDescription,
			'metaKeywords' => $this->metaKeywords,
			'menu' => $this->menuItem,
			'showSidebar' => $this->showSidebar,
			'sidebarOrientation' => $this->sidebarOrientation,
			'pageList' => $this->pageList,
			'isCommentable' => $this->isCommentable,
			'allowSubscribing' => $this->allowSubscribing,
			'availableStyles' => $this->availableStyles,
			'styleID' => $this->styleID,
			'stylesheets' => $this->stylesheets,
			'stylesheetList' => $this->stylesheetList->getObjects(),
			'enableDelayedDeactivation' => $this->enableDelayedDeactivation,
			'enableDelayedPublication' => $this->enableDelayedPublication,
			'deactivationDate' => $this->deactivationDate,
			'publicationDate' => $this->publicationDate
		));
	}
}
