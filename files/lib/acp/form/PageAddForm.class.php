<?php
namespace cms\acp\form;

use cms\data\layout\LayoutList;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
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
use wcf\util\StringUtil;

/**
 * Shows the page add form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageAddForm extends AbstractForm {

	public $templateName = 'pageAdd';

	public $neededPermissions = array(
		'admin.cms.page.canAddPage'
	);

	public $activeMenuItem = 'cms.acp.menu.link.cms.page.add';

	public $objectTypeID = 0;

	public $enableMultilangualism = true;

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

	public $menuItem = 1;

	public $menuItemID = null;

	public $pageList = null;

	public $layoutList = null;

	public $layoutID = 0;

	public $isCommentable = CMS_PAGES_DEFAULT_COMMENTS;

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

	public function readData() {
		parent::readData();
		if (isset($_REQUEST['id'])) $this->parentID = intval($_REQUEST['id']);

		$this->pageList = new PageNodeTree();
		$this->pageList = $this->pageList->getIterator();

		$this->layoutList = new LayoutList();
		$this->layoutList->readObjects();
		$this->layoutList = $this->layoutList->getObjects();
	}

	public function readFormParameters() {
		parent::readFormParameters();
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = StringUtil::trim(I18nHandler::getInstance()->getValue('description'));
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
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
		if (isset($_POST['layoutID'])) $this->layoutID = intval($_POST['layoutID']);
		if (isset($_POST['showSidebar'])) $this->showSidebar = intval($_POST['showSidebar']);
		else $this->showSidebar = 0;
		if (isset($_POST['sidebarOrientation'])) $this->sidebarOrientation = StringUtil::trim($_POST['sidebarOrientation']);
		if (isset($_POST['isCommentable'])) $this->isCommentable = intval($_POST['isCommentable']);
		else $this->isCommentable = 0;
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
	}

	public function validate() {
		parent::validate();

		if (! I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}

		// validate alias
		$this->validateAlias();

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
			'layoutID' => $this->layoutID,
			'parentID' => ($this->parentID) ?  : null,
			'showSidebar' => $this->showSidebar,
			'sidebarOrientation' => $this->sidebarOrientation,
			'robots' => $this->robots,
			'isCommentable' => $this->isCommentable,
			'styleID' => ($this->styleID) ?: null
		);

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

		$this->saved();
		WCF::getTPL()->assign('success', true);
		$this->title = $this->description = $this->metaDescription = $this->metaKeywords = $this->robots = $this->alias = '';
		$this->sidebarOrientation = 'right';
		$this->invisible = $this->parentID = $this->showOrder = $this->showSidebar = $this->styleID = 0;
		$this->menuItem = 1;
		I18nHandler::getInstance()->reset();
	}

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
			'alias' => $this->alias,
			'parentID' => $this->parentID,
			'showOrder' => $this->showOrder,
			'menu' => $this->menuItem,
			'layoutID' => $this->layoutID,
			'showSidebar' => $this->showSidebar,
			'sidebarOrientation' => $this->sidebarOrientation,
			'pageList' => $this->pageList,
			'layoutList' => $this->layoutList,
			'isCommentable' => $this->isCommentable,
			'availableStyles' => $this->availableStyles,
			'styleID' => $this->styleID
		));
	}
}
