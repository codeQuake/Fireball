<?php
namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\page\Page;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the content edit form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentEditForm extends ContentAddForm {
	public $contentID = 0;
	public $content = null;

	public function readData() {
		parent::readData();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$this->content = new Content($this->contentID);
		I18nHandler::getInstance()->setOptions('title', PACKAGE_ID, $this->content->title, 'cms.content.title\d+');
		$this->title = $this->content->title;
		$this->pageID = $this->content->pageID;
		$this->cssID = $this->content->cssID;
		$this->cssClasses = $this->content->cssClasses;
		$this->showOrder = $this->content->showOrder;
		$this->position = $this->content->position;
		$this->type = $this->content->type;
	}

	public function readParameters() {
		AbstractForm::readParameters();
		I18nHandler::getInstance()->register('title');
	}

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_REQUEST['id'])) $this->contentID = intval($_REQUEST['id']);
		$this->content = new Content($this->contentID);
	}

	public function save() {
		AbstractForm::save();
		$data = array(
			'title' => $this->title,
			'pageID' => $this->pageID,
			'cssID' => $this->cssID,
			'cssClasses' => $this->cssClasses,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'type' => $this->type
		);
		$objectAction = new ContentAction(array(
			$this->contentID
		), 'update', array(
			'data' => $data
		));
		$objectAction->executeAction();
		$update = array();
		if (! I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'cms.content.' . $this->contentID . '.title', 'cms.content', PACKAGE_ID);
			$update['title'] = 'cms.content.' . $this->contentID . '.title';
		}
		if (! empty($update)) {
			$editor = new ContentEditor(new Content($this->contentID));
			$editor->update($update);
		}
		$this->saved();
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('PageEdit', array('id' => $this->pageID),'#contents'));
		exit;
	}

	public function assignVariables() {
		parent::assignVariables();

		I18nHandler::getInstance()->assignVariables(! empty($_POST));
		WCF::getTPL()->assign(array(
			'contentID' => $this->contentID,
			'action' => 'edit',
			'page' => new Page($this->content->pageID),
			'content' => $this->content
		));
	}
}
