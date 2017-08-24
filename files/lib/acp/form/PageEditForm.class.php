<?php
namespace cms\acp\form;

use cms\data\page\DrainedPageNodeTree;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\PageNodeTree;
use cms\util\PageUtil;
use wcf\acp\form\AbstractAcpForm;
use wcf\data\menu\item\MenuItemAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the page edit form.
 * 
 * @author	Jens Krumsieck, Florian Frantzen, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageEditForm extends PageAddForm {
	/**
	 * @inheritDoc
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
	 * @inheritdoc
	 */
	public $createMenuItem = 0;
	
	/**
	 * @inheritDoc
	 */
	public $action = 'edit';
	
	/**
	 * @inheritDoc
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
	 * @inheritDoc
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
	 * @inheritDoc
	 */
	public function save() {
		AbstractAcpForm::save();

		$this->beforeSaveI18n($this->page);

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
			$data['publicationDate'] = @strtotime($this->publicationDate);
		} else {
			$data['isPublished'] = 1;
		}

		if ($this->enableDelayedDeactivation) {
			$data['isDisabled'] = 0;
			$data['deactivationDate'] = @strtotime($this->publicationDate);
		}

		$specificPageData =  $this->pageObjectType->getProcessor()->getSaveArray();
		$pageData = array_merge_recursive($specificPageData, [
			'data' => $data,
			'stylesheetIDs' => $this->stylesheetIDs
		]);

		$this->objectAction = new PageAction([$this->pageID], 'update', $pageData);
		$this->objectAction->executeAction();
		
		$this->saveI18n($this->page, PageEditor::class);

		// update menu item
		if ($this->menuItemID) {
			$menuItemAction = new MenuItemAction([$this->menuItemID], 'update', ['data' => [
				'title' => $this->title,
			]]);
			$menuItemAction->executeAction();
		} else {
			$this->createMenuItem($this->page->pageID, $this->title);
		}

		// save ACL
		ACLHandler::getInstance()->save($this->pageID, $this->objectTypeID);

		// create revision
		$objectAction = new PageAction([$this->pageID], 'createRevision', ['action' => 'update']);
		$objectAction->executeAction();

		// update search index
		$objectAction = new PageAction([$this->pageID], 'refreshSearchIndex');
		$objectAction->executeAction();

		$this->saved();
		
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @inheritDoc
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
			$this->readDataI18n($this->page);
			
			// general data
			$this->alias = $this->page->alias;
			
			// meta information
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
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'choosePageNodeList' => $this->choosePageNodeList,
			'pageID' => $this->pageID,
			'page' => $this->page
		]);
	}
}
