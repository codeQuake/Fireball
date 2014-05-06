<?php
namespace cms\acp\form;

use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\page\Page;
use cms\data\page\PageNodeTree;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the content add form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentAddForm extends AbstractForm {
	public $templateName = 'contentAdd';
	public $neededPermissions = array(
		'admin.cms.content.canAddContent'
	);
	public $activeMenuItem = 'cms.acp.menu.link.cms.content.add';
	public $enableMultilangualism = true;
	public $title = '';
	public $page = null;
	public $parentID = null;
	public $pageID = 0;
	public $showOrder = 0;
	public $cssID = '';
	public $cssClasses = '';
	public $contentData = array();

	public $pageList = null;

	public $objectType;
	public $objectTypeProcessor;

	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('title');
		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		if (isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', $_REQUEST['objectType']);
			if ($this->objectType != null) {
					$this->objectTypeProcessor = $this->objectType->getProcessor();
			}
	}

	public function readData() {
		parent::readData();
		$nodeTree = new PageNodeTree();
		$this->pageList = $nodeTree->getIterator();
	}

	public function readFormParameters() {
		parent::readFormParameters();
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		if (isset($_POST['cssID'])) $this->cssID = StringUtil::trim($_POST['cssID']);
		if (isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['contentData']) && is_array($_POST['contentData'])) $this->contentData = $_POST['contentData'];

	}

	public function validate() {
		parent::validate();
		$this->objectTypeProcessor->validate();

		if (! I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}
		$this->page = new Page($this->pageID);
		if ($this->page === null) throw new UserInputException('pageID', 'invalid');
	}

	public function save() {
		parent::save();
		$data = array(
			'title' => $this->title,
			'pageID' => $this->pageID,
			'parentID' => $this->parentID,
			'cssID' => $this->cssID,
			'cssClasses' => $this->cssClasses,
			'showOrder' => $this->showOrder,
			'contentData' => serialize($this->contentData),
			'contentTypeID' => $this->objectType->objectTypeID
		);
		$objectAction = new ContentAction(array(), 'create', array(
			'data' => $data
		));
		$objectAction->executeAction();
		$returnValues = $objectAction->getReturnValues();
		$contentID = $returnValues['returnValues']->contentID;
		$update = array();
		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.' . $contentID . '.title', 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.' . $contentID . '.title';
		}
		if (! empty($update)) {
			$editor = new ContentEditor($returnValues['returnValues']);
			$editor->update($update);
		}

		$this->saved();

	}

	public function assignVariables() {
		parent::assignVariables();
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'cssClasses' => $this->cssClasses,
			'cssID' => $this->cssID,
			'showOrder' => $this->showOrder,
			'pageID' => $this->pageID,
			'parentID' => $this->parentID,
			'pageList' => $this->pageList,
			'page' => new Page($this->pageID),
			'objectType' => $this->objectType,
			'objectTypeProcessor' => $this->objectTypeProcessor,
			'contentData' => $this->contentData
		));
	}
}
