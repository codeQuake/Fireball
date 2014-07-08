<?php
namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentCache;
use cms\data\content\ContentEditor;
use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\Page;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the content edit form.
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ContentEditForm extends ContentAddForm {

	public $contentID = 0;

	public $content = null;

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
	}

	public function readData() {
		parent::readData();
		$this->content = ContentCache::getInstance()->getContent($this->contentID);
		$this->pageID = $this->content->pageID;
		$this->cssClasses = $this->content->cssClasses;
		$this->cssID = $this->content->cssID;
		$this->parentID = $this->content->parentID;
		$this->showOrder = $this->content->showOrder;
		$this->position = $this->content->position;
		$this->contentData = $this->content->handleContentData();
		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') PollManager::getInstance()->setObject('de.codequake.cms.content', $this->content->contentID, $this->contentData['pollID']);
		$this->title = $this->content->getTitle();
		I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->content->title, 'cms.content.title\d+');
		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				I18nHandler::getInstance()->setOptions($field, PACKAGE_ID, $this->contentData[$field], 'cms.content.' . $field . '\d+');
			}
		}
		//overwrite contentlist
		$this->contentList = new DrainedPositionContentNodeTree(null, $this->pageID, $this->contentID, $this->position);
		$this->contentList = $this->contentList->getIterator();
	}

	public function save() {
		AbstractForm::save();
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
		$objectAction = new ContentAction(array(
			$this->contentID
		), 'update', array(
			'data' => $data
		));
		
		$objectAction->executeAction();
		$contentID = $this->contentID;
		$content = new Content($contentID);
		$contentData = @unserialize($content->contentData);
		
		$update = array();
		
		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') {
			$pollID = PollManager::getInstance()->save($this->content->contentID);
			if ($pollID && $pollID != $contentData['pollID']) {
				$contentData['pollID'] = $pollID;
			
			} 			//happens for idiots :P
			else if (! $pollID && $contentData['pollID']) {
				$contentData['pollID'] = null;
			}
		}
		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.title' . $contentID, 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.title' . $contentID;
		}
		
		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				if (! I18nHandler::getInstance()->isPlainValue($field)) {
					I18nHandler::getInstance()->save($field, 'cms.content.' . $field . $contentID, 'cms.content', PACKAGE_ID);
					$contentData[$field] = 'cms.content.' . $field . $contentID;
				}
			}
		}
		
		$update['contentData'] = serialize($contentData);
		if (! empty($update)) {
			$editor = new ContentEditor($content);
			$editor->update($update);
		}
		
		//create revision
		$objectAction = new ContentAction(array(
			$this->contentID
		), 'createRevision', array(
			'action' => 'update'
		));
		$objectAction->executeAction();
		$this->saved();
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ContentList', array(
			'application' => 'cms',
			'object' => new Page($this->pageID)
		)));
	}

	public function assignVariables() {
		AbstractForm::assignVariables();
		I18nHandler::getInstance()->assignVariables(! empty($_POST));
		if ($this->objectType->objectType == 'de.codequake.cms.content.type.poll') PollManager::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'cssClasses' => $this->cssClasses,
			'cssID' => $this->cssID,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'pageID' => $this->pageID,
			'parentID' => $this->parentID,
			'contentList' => $this->contentList,
			'page' => new Page($this->pageID),
			'objectType' => $this->objectType,
			'objectTypeProcessor' => $this->objectTypeProcessor,
			'contentData' => $this->contentData,
			'contentID' => $this->contentID
		));
	}
}
