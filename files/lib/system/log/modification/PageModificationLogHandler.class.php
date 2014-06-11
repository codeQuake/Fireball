<?php
namespace cms\system\log\modification;

use cms\data\content\Content;
use cms\data\page\Page;
use wcf\system\log\modification\ModificationLogHandler;
use wcf\system\WCF;

class PageModificationLogHandler extends ModificationLogHandler {

	public function create(Page $page) {
		$this->add($page, 'create', array(
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
		));
	}

	public function edit(Page $page) {
		$this->add($page, 'edit', array(
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
			));
	}

	public function addContent(Page $page, Content $content) {
		$this->add($page, 'addContent', array(
			'content' => $content,
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
		));
	}

	public function editContent(Page $page, Content $content) {
		$this->add($page, 'editContent', array(
			'content' => $content,
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
		));
	}

	public function sortContents(Page $page) {
		$this->add($page, 'sortContents', array(
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
		));
	}

	public function deleteContent(Page $page) {
		$this->add($page, 'deleteContent', array(
			'time' => TIME_NOW,
			'username' => WCF::getUser()->username
		));
	}

	public function add(Page $page, $action, array $additionalData = array()) {
		parent::_add('de.codequake.cms.page', $page->pageID, $action, $additionalData);
	}
}
