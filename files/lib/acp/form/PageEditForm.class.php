<?php
namespace cms\acp\form;
use cms\data\page\DrainedPageNodeTree;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
use cms\util\PageUtil;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\data\page\menu\item\PageMenuItemAction;
use wcf\data\page\menu\item\PageMenuItemEditor;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the page edit form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageEditForm extends PageAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.content';

	/**
	 * page node list for 'choose page' button
	 * @var	\RecursiveIteratorIterator
	 */
	public $choosePageNodeList = null;

	/**
	 * page id
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * page object
	 * @var	\cms\data\page\Page
	 */
	public $page = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		$this->page = new Page($this->pageID);
		if (!$this->page->pageID) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see \cms\acp\form\PageAddForm::validateAlias()
	 */
	protected function validateAlias() {
		if (empty($this->alias)) {
			throw new UserInputException('alias');
		}
		if (!PageUtil::isValidAlias($this->alias)) {
			throw new UserInputException('alias', 'notValid');
		}
		if (!PageUtil::isAvailableAlias($this->alias, ($this->parentID) ?: null, $this->pageID)) {
			throw new UserInputException('alias', 'notUnique');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();

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
			$data['publicationDate'] = @strtotime($this->publicationDate);
		} else {
			$data['isPublished'] = 1;
		}
		if ($this->enableDelayedDeactivation) {
			$data['isDisabled'] = 0;
			$data['deactivationDate'] = @strtotime($this->publicationDate);
		}

		$pageData = array(
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		);

		$this->objectAction = new PageAction(array($this->pageID), 'update', $pageData);
		$this->objectAction->executeAction();

		$update = array();

		// save ACL
		ACLHandler::getInstance()->save($this->pageID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();

		// update I18n
		I18nHandler::getInstance()->save('title', 'cms.page.title' . $this->pageID, 'cms.page');
		$update['title'] = 'cms.page.title' . $this->pageID;

		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->save('description', 'cms.page.description' . $this->pageID, 'cms.page');
			$update['description'] = 'cms.page.description' . $this->pageID;
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaDescription')) {
			I18nHandler::getInstance()->save('metaDescription', 'cms.page.metaDescription' . $this->pageID, 'cms.page');
			$update['metaDescription'] = 'cms.page.metaDescription' . $this->pageID;
		}
		if (!I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
			I18nHandler::getInstance()->save('metaKeywords', 'cms.page.metaKeywords' . $this->pageID, 'cms.page');
			$update['metaKeywords'] = 'cms.page.metaKeywords' . $this->pageID;
		}

		$page = new Page($this->pageID);
		$this->menuItemID = $page->menuItemID;

		if (!$this->menuItem && $this->menuItemID) {
			// delete old item
			$action = new PageMenuItemAction(array(
				$this->menuItemID
			), 'delete', array());
			$action->executeAction();

			$update['menuItemID'] = null;
		}
		else if ($this->menuItem && !$this->menuItemID) {
			// create menuitem
			$page = new Page($this->pageID);
			if ($page->getParentPage() !== null) {
				$parentPage = $page->getParentPage();
				$parentItem = new PageMenuItem($parentPage->menuItemID);
			}

			$data = array(
				'className' => 'cms\system\menu\page\CMSPageMenuItemProvider',
				'menuItemController' => 'cms\page\PagePage',
				'menuItemLink' => 'id=' . $this->pageID,
				'menuPosition' => 'header',
				'options' => '',
				'permissions' => '',
				'packageID' => PACKAGE_ID,
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
		else if ($this->menuItem && $this->menuItemID) {
			//update old item
			$item = new PageMenuItem($this->menuItemID);
			$editor = new PageMenuItemEditor($item);
			I18nHandler::getInstance()->save('title', 'wcf.page.menuItem.' . $item->menuItemID, 'wcf.page');
			$menuData['menuItem'] = 'wcf.page.menuItem.' . $item->menuItemID;
			$editor->update($menuData);
		}

		if (!empty($update)) {
			$editor = new PageEditor(new Page($this->pageID));
			$editor->update($update);
		}

		//create revision
		$objectAction = new PageAction(array(
			$this->pageID
		), 'createRevision', array(
			'action' => 'update'
		));
		$objectAction->executeAction();

		//update search index
		$objectAction = new PageAction(array($this->pageID), 'refreshSearchIndex');
		$objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// overwrite page list
		$pageNodeTree = new DrainedPageNodeTree(null, $this->pageID);
		$this->pageList = $pageNodeTree->getIterator();

		// read page node list for 'choose page' button
		$choosePageNodeTree = new PageNodeTree();
		$this->choosePageNodeList = $choosePageNodeTree->getIterator();

		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->page->title, 'cms.page.title\d+');
			I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->page->description, 'cms.page.description\d+');
			$this->description = $this->page->description;
			I18nHandler::getInstance()->setOptions('metaDescription', PACKAGE_ID, $this->page->metaDescription, 'cms.page.metaDescription\d+');
			$this->metaDescription = $this->page->metaDescription;
			I18nHandler::getInstance()->setOptions('metaKeywords', PACKAGE_ID, $this->page->metaKeywords, 'cms.page.metaKeywords\d+');
			$this->metaKeywords = $this->page->metaKeywords;

			$this->parentID = $this->page->parentID;
			$this->showOrder = $this->page->showOrder;
			$this->invisible = $this->page->invisible;
			$this->allowIndexing = $this->page->allowIndexing;
			$this->sidebarOrientation = $this->page->sidebarOrientation;
			$this->isCommentable = $this->page->isCommentable;
			$this->allowSubscribing = $this->page->allowSubscribing;
			$this->availableDuringOfflineMode = $this->page->availableDuringOfflineMode;
			$this->menuItem = $this->page->menuItemID !== null ? 1 : 0;
			$this->menuItemID = $this->page->menuItemID;

			$this->alias = $this->page->alias;
			$this->styleID = $this->page->styleID;
			$this->stylesheetIDs = $this->page->getStylesheetIDs();

			// publication
			if (!$this->page->isPublished) {
				$this->enableDelayedPublication = 1;

				$dateTime = DateUtil::getDateTimeByTimestamp($this->page->publicationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->publicationDate = $dateTime->format('c');
			}
			if ($this->page->deactivationDate) {
				$this->enableDelayedDeactivation = 1;

				$dateTime = DateUtil::getDateTimeByTimestamp($this->page->deactivationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->deactivationDate = $dateTime->format('c');
			}
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		I18nHandler::getInstance()->assignVariables(!empty($_POST));

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'choosePageNodeList' => $this->choosePageNodeList,
			'pageID' => $this->pageID,
			'page' => $this->page
		));
	}
}
