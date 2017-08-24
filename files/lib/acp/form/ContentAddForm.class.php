<?php
namespace cms\acp\form;

use cms\data\content\ContentAction;
use cms\data\content\ContentCache;
use cms\data\content\ContentEditor;
use cms\data\content\ContentNodeTree;
use cms\data\page\Page;
use cms\data\page\PageAction;
use cms\data\page\PageNodeTree;
use wcf\acp\form\AbstractAcpForm;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\PackageCache;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\language\I18nValue;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the content add form.
 * 
 * @author	Jens Krumsieck, Florian Frantzen, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentAddForm extends AbstractAcpForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.list';

	/**
	 * content data
	 * @var	array<mixed>
	 */
	public $contentData = [];

	/**
	 * list of contents
	 * @var	\RecursiveIteratorIterator
	 */
	public $contentList = null;

	/**
	 * css classes of the content
	 * @var	string
	 */
	public $cssClasses = '';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.content.canAddContent'];

	/**
	 * content object type
	 * @var	\wcf\data\object\type\ObjectType
	 */
	public $objectType = null;

	/**
	 * id of the page the cotent will be assigned to
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * id of the parent content
	 * @var	integer
	 */
	public $parentID = null;

	/**
	 * position of the new content ('body' or 'sidebar')
	 * @var	string
	 */
	public $position = 'body';

	/**
	 * show order
	 * @var	integer
	 */
	public $showOrder = 0;

	/**
	 * content title
	 * @var	string
	 */
	public $title = '';

	/**
	 * show title
	 * @var boolean
	 */
	public $showHeadline = 0;

	/**
	 * object type id of content
	 * @var integer
	 */
	public $contentObjectTypeID = null;

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		// form values that can be specified as get parameters
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		if (isset($_REQUEST['position'])) $this->position = StringUtil::trim($_REQUEST['position']);
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		if (isset($_REQUEST['showHeadline'])) $this->showHeadline = 1;

		if ($this->objectType === null) {
			if (isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', $_REQUEST['objectType']);
			if ($this->objectType === null || !$this->objectType->getProcessor()->isAvailableToAdd($this->position)) {
				throw new IllegalLinkException();
			}
		}
		
		$packageID = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms');
		
		$parentIsTabMenu = false;
		if ($this->parentID) {
			$parent = ContentCache::getInstance()->getContent($this->parentID);
			//check if parent is tab menu
			$parentIsTabMenu = ($parent->contentTypeID == ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.tabmenu'));
		}
		
		$i18nTitle = new I18nValue('title');
		$i18nTitle->setLanguageItem('cms.page.title', 'cms.page', $packageID);
		if (!($this->objectType->getProcessor()->requiresTitle || in_array($this->position, ['sidebarLeft', 'sidebarRight']) || $parentIsTabMenu)) $i18nTitle->setFlags(I18nValue::ALLOW_EMPTY);
		$this->registerI18nValue($i18nTitle);

		// get acl object type id
		$this->contentObjectTypeID = ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.content');

		// read object type specific parameters
		$this->objectType->getProcessor()->readParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['contentData']) && is_array($_POST['contentData'])) $this->contentData = $_POST['contentData'];

		foreach ($this->objectType->getProcessor()->multilingualFields as $field) {
			if (I18nHandler::getInstance()->isPlainValue($field)) $this->contentData[$field] = StringUtil::trim(I18nHandler::getInstance()->getValue($field));
		}

		// read object type specific form parameters
		$this->objectType->getProcessor()->readFormParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

		// validate position
		if (!in_array($this->position, $this->objectType->getProcessor()->availablePositions)) {
			throw new UserInputException('position');
		}

		// validate show order
		if ($this->showOrder == 0) {
			$childIDs = ContentCache::getInstance()->getChildIDs($this->parentID ?: null);
			if (!empty($childIDs)) {
				$showOrders = [];
				foreach ($childIDs as $childID) {
					$content = ContentCache::getInstance()->getContent($childID);
					$showOrders[] = $content->showOrder;
				}
				array_unique($showOrders);
				if (isset($this->contentID)) {
					$content = ContentCache::getInstance()->getContent($this->contentID);
					if ($content->showOrder == max($showOrders) && max($showOrders) != 0) $this->showOrder = max($showOrders);
					else $this->showOrder = intval(max($showOrders) + 1);
				}
				else
					$this->showOrder = intval(max($showOrders) + 1);
			}
			else
				$this->showOrder = 1;
		}

		$parent = null;
		
		$page = new Page($this->pageID);
		if (!$page->pageID) {
			throw new UserInputException('pageID', 'invalid');
		}
		
		$this->contentData['i18nValues'] = [];
		foreach ($this->objectType->getProcessor()->multilingualFields as $field) {
			if (!I18nHandler::getInstance()->isPlainValue($field)) {
				$this->contentData['i18nValues'][$field] = StringUtil::trim(I18nHandler::getInstance()->getValues($field));
			}
		}
		
		// validate object type specific parameters
		$this->objectType->getProcessor()->validate($this->contentData);
		
		unset($this->contentData['i18nValues']);
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$data = [
			'title' => $this->title,
			'pageID' => $this->pageID,
			'parentID' => ($this->parentID) ?  : null,
			'cssClasses' => $this->cssClasses,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'contentData' => $this->contentData,
			'contentTypeID' => $this->objectType->objectTypeID,
			'showHeadline' => $this->showHeadline
		];

		$this->objectAction = new ContentAction([], 'create', [
			'data' => $data
		]);
		$returnValues = $this->objectAction->executeAction();

		$contentID = $returnValues['returnValues']->contentID;
		$contentData = $returnValues['returnValues']->contentData;
		$update = [];

		// save polls
		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') {
			$pollID = PollManager::getInstance()->save($returnValues['returnValues']->contentID);
			if ($pollID) {
				$contentData['pollID'] = $pollID;
			}
		}
		
		$this->saveI18n($returnValues['returnValues'], ContentEditor::class);
		
		foreach ($this->objectType->getProcessor()->multilingualFields as $field) {
			if (!I18nHandler::getInstance()->isPlainValue($field)) {
				I18nHandler::getInstance()->save($field, 'cms.content.' . $field . $contentID, 'cms.content', PACKAGE_ID);
				$contentData[$field] = 'cms.content.' . $field . $contentID;
			}
		}

		$update['contentData'] = serialize($contentData);

		if (!empty($update)) {
			$editor = new ContentEditor($returnValues['returnValues']);
			$editor->update($update);
		}

		// create revision
		$objectAction = new PageAction([$this->pageID], 'createRevision', [
			'action' => 'content.create'
		]);
		$objectAction->executeAction();

		// update search index
		$objectAction = new PageAction([$returnValues['returnValues']->pageID], 'refreshSearchIndex');
		$objectAction->executeAction();

		// save ACL values of the content
		ACLHandler::getInstance()->save($returnValues['returnValues']->contentID, $this->contentObjectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();
		
		$this->reset();
	}
	
	/**
	 * @inheritDoc
	 */
	public function reset() {
		parent::reset();
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ContentList', [
			'application' => 'cms',
			'id' => $this->pageID
		], '#' . $this->position));
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// read page list
		$pageNodeTree = new PageNodeTree();
		$this->pageList = $pageNodeTree->getIterator();

		// read content list
		$contentNodeTree = new ContentNodeTree(null, 0, 1);
		$this->contentList = $contentNodeTree->getIterator();
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		ACLHandler::getInstance()->assignVariables($this->contentObjectTypeID);

		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') {
			PollManager::getInstance()->assignVariables();
		}

		WCF::getTPL()->assign([
			'action' => 'add',
			'contentData' => $this->contentData,
			'contentList' => $this->contentList,
			'cssClasses' => $this->cssClasses,
			'objectType' => $this->objectType,
			'pageID' => $this->pageID,
			'pageList' => $this->pageList,
			'parentID' => $this->parentID,
			'position' => $this->position,
			'showOrder' => $this->showOrder,
			'showHeadline' => $this->showHeadline,
			'contentObjectTypeID' => $this->contentObjectTypeID
		]);
	}
}
