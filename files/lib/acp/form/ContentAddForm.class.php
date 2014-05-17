<?php
namespace cms\acp\form;

use cms\data\content\ContentAction;
use cms\data\content\ContentCache;
use cms\data\content\ContentEditor;
use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\Page;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
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
	public $position = 'body';
	public $pageID = 0;
	public $showOrder = 0;
	public $cssID = '';
	public $cssClasses = '';
	public $contentData = array();

	public $contentList = null;

	public $objectType;
	public $objectTypeProcessor;

	public function readParameters() {
		parent::readParameters();
		I18nHandler::getInstance()->register('title');
		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		if (isset($_REQUEST['position'])) $this->position = StringUtil::trim($_REQUEST['position']);
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		if ($this->parentID == 0) $this->parentID = null;
		if (isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', $_REQUEST['objectType']);
			if ($this->objectType != null) {
					$this->objectTypeProcessor = $this->objectType->getProcessor();
			}
		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				I18nHandler::getInstance()->register($field);
			}
		}
	}

	public function readData() {
		parent::readData();
		$this->contentList = new DrainedPositionContentNodeTree(null, $this->pageID, null, $this->position);
		$this->contentList = $this->contentList->getIterator();
	}

	public function readFormParameters() {
		parent::readFormParameters();
		I18nHandler::getInstance()->readValues();
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = StringUtil::trim(I18nHandler::getInstance()->getValue('title'));
		if (isset($_REQUEST['pageID'])) $this->pageID = intval($_REQUEST['pageID']);
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		if (isset($_POST['cssID'])) $this->cssID = StringUtil::trim($_POST['cssID']);
		if (isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
		if (isset($_POST['position'])) $this->position = StringUtil::trim($_POST['position']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
		if (isset($_POST['contentData']) && is_array($_POST['contentData'])) $this->contentData = $_POST['contentData'];
		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				if(I18nHandler::getInstance()->isPlainValue($field)) $this->contentData[$field] = StringUtil::trim(I18nHandler::getInstance()->getValue($field));
			}
		}
	}

	public function validate() {
		parent::validate();
		$this->objectTypeProcessor->validate($this->contentData);

		//if this happens, user is a retard
		$position = array('body', 'sidebar');
		if(!in_array($this->position, $position)) throw new UserInputException('position');
		if($this->position == 'sidebar' && !$this->objectType->allowsidebar) throw new UserInputException('position');
		if($this->position == 'body' && !$this->objectType->allowcontent) throw new UserInputException('position');

		//validate showOrder
		if ($this->showOrder == 0) {
			$childIDs = ContentCache::getInstance()->getChildIDs($this->parentID ?: null);
			if (! empty($childIDs)) {
				$showOrders = array();
				foreach ($childIDs as $childID) {
					$content = ContentCache::getInstance()->getContent($childID);
					$showOrders[] = $content->showOrder;
				}
				array_unique($showOrders);
				if (isset($this->contentID)) {
					$content = ContentCache::getInstance()->getContent($this->contentID);
					if($content->showOrder == max($showOrders) && max($showOrders) != 0) $this->showOrder = max($showOrders);
					else $this->showOrder = intval(max($showOrders) + 1);
				}
				else $this->showOrder = intval(max($showOrders) + 1);
			}
			else $this->showOrder = 1;
		}

		if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}

		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				if (!I18nHandler::getInstance()->validateValue($field)) {
					if (I18nHandler::getInstance()->isPlainValue($field)) {
						throw new UserInputException($field);
					}
					else {
						throw new UserInputException($field, 'multilingual');
					}
				}
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
			'parentID' => ($this->parentID) ?  : null,
			'cssID' => $this->cssID,
			'cssClasses' => $this->cssClasses,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'contentData' => serialize($this->contentData),
			'contentTypeID' => $this->objectType->objectTypeID
		);
		$objectAction = new ContentAction(array(), 'create', array(
			'data' => $data
		));
		$objectAction->executeAction();
		$returnValues = $objectAction->getReturnValues();
		$contentID = $returnValues['returnValues']->contentID;
		$contentData = @unserialize($returnValues['returnValues']->contentData);
		$update = array();
		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.title'.$contentID, 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.title'.$contentID;
		}

		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				if (! I18nHandler::getInstance()->isPlainValue($field)) {
					I18nHandler::getInstance()->save($field, 'cms.content.'.$field.$contentID, 'cms.content', PACKAGE_ID);
					$contentData[$field] = 'cms.content.'.$field.$contentID;
				}
			}
			$update['contentData'] = serialize($contentData);
		}
		if (! empty($update)) {
			$editor = new ContentEditor($returnValues['returnValues']);
			$editor->update($update);
		}

		$this->saved();
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ContentList', array('application' => 'cms', 'object' => new Page($this->pageID))));

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
			'position' => $this->position,
			'contentList' => $this->contentList,
			'page' => new Page($this->pageID),
			'objectType' => $this->objectType,
			'objectTypeProcessor' => $this->objectTypeProcessor,
			'contentData' => $this->contentData
		));
	}
}
