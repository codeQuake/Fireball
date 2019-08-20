<?php
namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\PageAction;
use wcf\acp\form\AbstractAcpForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * Shows the content edit form.
 * 
 * @author	Jens Krumsieck, Florian Frantzen, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentEditForm extends ContentAddForm {
	/**
	 * content id
	 * @var	integer
	 */
	public $contentID = 0;

	/**
	 * content object
	 * @var	\cms\data\content\Content
	 */
	public $content = null;
	
	/**
	 * @inheritDoc
	 */
	public $action = 'edit';
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$this->content = new Content($this->contentID);
		if (!$this->content->contentID) {
			throw new IllegalLinkException();
		}

		$this->objectType = $this->content->getObjectType();

		parent::readParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractAcpForm::save();

		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') {
			$this->contentData['pollID'] = PollManager::getInstance()->save($this->contentID);
		}

		$this->beforeSaveI18n($this->content);

		foreach ($this->objectType->getProcessor()->multilingualFields as $field) {
			$languageVariable = 'cms.content.' . $field . $this->contentID;
			if (I18nHandler::getInstance()->isPlainValue($field)) {
				I18nHandler::getInstance()->remove($languageVariable);
			} else {
				I18nHandler::getInstance()->save($field, $languageVariable, 'cms.content', PACKAGE_ID);
				$this->contentData[$field] = $languageVariable;
			}
		}

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

		$this->objectAction = new ContentAction([$this->contentID], 'update', [
			'data' => $data
		]);
		$this->objectAction->executeAction();
		
		$this->saveI18n($this->content, ContentEditor::class);

		// create revision
		if ($this->pageID == $this->content->pageID) {
			$objectAction = new PageAction([$this->pageID], 'createRevision', [
				'action' => 'content.update'
			]);
			$objectAction->executeAction();
		} else {
			$objectAction = new PageAction([$this->pageID], 'createRevision', [
				'action' => 'content.create'
			]);
			$objectAction->executeAction();

			$objectAction = new PageAction([$this->content->pageID], 'createRevision', [
				'action' => 'content.delete'
			]);
			$objectAction->executeAction();
		}

		// update search index
		$objectAction = new PageAction([$this->pageID], 'refreshSearchIndex');
		$objectAction->executeAction();

		// save ACL values of the content
		ACLHandler::getInstance()->save($this->contentID, $this->contentObjectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();

		$this->reset();
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		if (empty($_POST)) {
			$this->readDataI18n($this->content);
			
			$this->pageID = $this->content->pageID;
			$this->cssClasses = $this->content->cssClasses;
			$this->parentID = $this->content->parentID;
			$this->showOrder = $this->content->showOrder;
			$this->position = $this->content->position;
			$this->contentData = $this->content->contentData;
			$this->showHeadline = $this->content->showHeadline;

			if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') {
				PollManager::getInstance()->setObject('de.codequake.cms.content', $this->content->contentID, $this->contentData['pollID']);
			}

			foreach ($this->objectType->getProcessor()->multilingualFields as $field) {
				I18nHandler::getInstance()->setOptions($field, PACKAGE_ID, $this->contentData[$field], 'cms.content.' . $field . '\d+');
			}
		}

		// overwrite content list
		$this->contentList = new DrainedPositionContentNodeTree(null, $this->pageID, $this->contentID, $this->position);
		$this->contentList = $this->contentList->getIterator();
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'contentID' => $this->contentID
		]);
	}
}
