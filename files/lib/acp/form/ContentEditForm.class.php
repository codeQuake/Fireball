<?php
namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentCache;
use cms\data\content\ContentEditor;
use cms\data\content\DrainedContentNodeTree;
use cms\data\page\Page;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

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
		$this->contentData = $this->content->handleContentData();
		$this->title = $this->content->getTitle();
		I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->content->title, 'cms.content.title\d+');
		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				I18nHandler::getInstance()->setOptions($field, PACKAGE_ID, $this->contentData[$field], 'cms.content.' . $field . '\d+');
			}
		}
		//overwrite contentlist
		$this->contentList = new DrainedContentNodeTree(null, $this->pageID, $this->contentID);
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
		if (!I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.title' . $contentID, 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.title' . $contentID;
		}

		if ($this->objectTypeProcessor->isMultilingual) {
			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				if (!I18nHandler::getInstance()->isPlainValue($field)) {
					I18nHandler::getInstance()->save($field, 'cms.content.' . $field . $contentID, 'cms.content', PACKAGE_ID);
					$contentData[$field] = 'cms.content.' . $field . $contentID;
				}
			}
			$update['contentData'] = serialize($contentData);
		}
		if (!empty($update)) {
			$editor = new ContentEditor($content);
			$editor->update($update);
		}

		$this->saved();
		WCF::getTPL()->assign('success', true);
	}

	public function assignVariables() {
		AbstractForm::assignVariables();
		I18nHandler::getInstance()->assignVariables(! empty($_POST));
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'cssClasses' => $this->cssClasses,
			'cssID' => $this->cssID,
			'showOrder' => $this->showOrder,
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
