<?php
namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentCache;
use cms\data\content\ContentEditor;
use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\Page;
use cms\data\page\PageAction;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the content edit form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
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
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
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
			$pollID = PollManager::getInstance()->save($contentID);
			if ($pollID && $pollID != $contentData['pollID']) {
				$contentData['pollID'] = $pollID;

			} //happens for idiots :P
			else if (! $pollID && $contentData['pollID']) {
				$contentData['pollID'] = null;
			}
		}
		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.title' . $contentID, 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.title' . $contentID;
		}

		foreach ($this->objectTypeProcessor->multilingualFields as $field) {
			if (!I18nHandler::getInstance()->isPlainValue($field)) {
				I18nHandler::getInstance()->save($field, 'cms.content.' . $field . $contentID, 'cms.content', PACKAGE_ID);
				$contentData[$field] = 'cms.content.' . $field . $contentID;
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

		//update search index
		$objectAction = new PageAction(array($this->pageID), 'refreshSearchIndex');
		$objectAction->executeAction();

		$this->saved();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ContentList', array(
			'application' => 'cms',
			'pageID' => $this->pageID
		)));
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		if (empty($_POST)) {
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

			foreach ($this->objectTypeProcessor->multilingualFields as $field) {
				I18nHandler::getInstance()->setOptions($field, PACKAGE_ID, $this->contentData[$field], 'cms.content.' . $field . '\d+');
			}
		}

		//overwrite contentlist
		$this->contentList = new DrainedPositionContentNodeTree(null, $this->pageID, $this->contentID, $this->position);
		$this->contentList = $this->contentList->getIterator();
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		I18nHandler::getInstance()->assignVariables(!empty($_POST));

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'contentID' => $this->contentID
		));
	}
}
