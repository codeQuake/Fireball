<?php
namespace cms\acp\form;

use cms\data\page\DrainedPageNodeTree;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageNodeTree;
use cms\util\PageUtil;
use wcf\data\menu\item\MenuItemAction;
use wcf\data\object\type\ObjectTypeCache;
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
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageEditForm extends PageAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page';

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
		
		if (empty($_POST)) {
			$this->pageObjectType = ObjectTypeCache::getInstance()->getObjectType($this->page->objectTypeID);
			$this->pageObjectTypeID = $this->pageObjectType->objectTypeID;
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

		// save multilingual inputs
		$languageVariable = 'cms.page.title'.$this->pageID;
		if (I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->remove($languageVariable);
		} else {
			I18nHandler::getInstance()->save('title', $languageVariable, 'cms.page');
			$this->title = $languageVariable;
		}

		$languageVariable = 'cms.page.description'.$this->pageID;
		if (I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->remove($languageVariable);
		} else {
			I18nHandler::getInstance()->save('description', $languageVariable, 'cms.page');
			$this->description = $languageVariable;
		}

		$languageVariable = 'cms.page.metaDescription'.$this->pageID;
		if (I18nHandler::getInstance()->isPlainValue('metaDescription')) {
			I18nHandler::getInstance()->remove($languageVariable);
		} else {
			I18nHandler::getInstance()->save('metaDescription', $languageVariable, 'cms.page');
			$this->metaDescription = $languageVariable;
		}

		$languageVariable = 'cms.page.metaKeywords'.$this->pageID;
		if (I18nHandler::getInstance()->isPlainValue('metaKeywords')) {
			I18nHandler::getInstance()->remove($languageVariable);
		} else {
			I18nHandler::getInstance()->save('metaKeywords', $languageVariable, 'cms.page');
			$this->metaKeywords = $languageVariable;
		}

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
			$data['publicationDate'] = @strtotime($this->publicationDate);
		} else {
			$data['isPublished'] = 1;
		}

		if ($this->enableDelayedDeactivation) {
			$data['isDisabled'] = 0;
			$data['deactivationDate'] = @strtotime($this->publicationDate);
		}

		$specificPageData =  $this->pageObjectType->getProcessor()->getSaveArray();
		$pageData = array_merge_recursive($specificPageData, array(
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		));

		$this->objectAction = new PageAction(array($this->pageID), 'update', $pageData);
		$this->objectAction->executeAction();

		// update menu item
		if ($this->menuItemID) {
			$menuItemAction = new MenuItemAction(array($this->menuItemID), 'update', array('data' => array(
				'title' => $this->title,
			)));
			$menuItemAction->executeAction();
		} else {
			$this->createMenuItem($this->page, $this->title);
		}

		// save ACL
		ACLHandler::getInstance()->save($this->pageID, $this->objectTypeID);

		// create revision
		$objectAction = new PageAction(array($this->pageID), 'createRevision', array('action' => 'update'));
		$objectAction->executeAction();

		// update search index
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
			// general data
			I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->page->title, 'cms.page.title\d+');
			$this->alias = $this->page->alias;
			I18nHandler::getInstance()->setOptions('description', PACKAGE_ID, $this->page->description, 'cms.page.description\d+');

			// meta information
			I18nHandler::getInstance()->setOptions('metaDescription', PACKAGE_ID, $this->page->metaDescription, 'cms.page.metaDescription\d+');
			I18nHandler::getInstance()->setOptions('metaKeywords', PACKAGE_ID, $this->page->metaKeywords, 'cms.page.metaKeywords\d+');
			$this->allowIndexing = $this->page->allowIndexing;

			// position
			$this->parentID = $this->page->parentID;
			$this->showOrder = $this->page->showOrder;
			$this->invisible = $this->page->invisible;

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

			// settings
			$this->menuItemID = $this->page->menuItemID;
			$this->availableDuringOfflineMode = $this->page->availableDuringOfflineMode;
			$this->allowSubscribing = $this->page->allowSubscribing;

			// display
			$this->styleID = $this->page->styleID;
			$this->stylesheetIDs = $this->page->getStylesheetIDs();

			// display settings
			$this->sidebarOrientation = $this->page->sidebarOrientation;
			
			// page type
			$this->pageObjectTypeID = $this->page->objectTypeID;
			$this->pageObjectType = ObjectTypeCache::getInstance()->getObjectType($this->pageObjectTypeID);
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
