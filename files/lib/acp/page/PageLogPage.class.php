<?php
namespace cms\acp\page;

use cms\data\page\PageCache;
use wcf\page\SortablePage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

class PageLogPage extends SortablePage {

	public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	public $validSortFields = array(
		'logID',
		'time',
		'username'
	);

	public $pageID = 0;
	public $page = null;
	public $objectListClassName = 'cms\data\modification\log\PageModificationLogList';

	public function readParameters() {
		parent::readParameters();
		if (isset($_GET['id'])) $this->pageID = intval($_GET['id']);
		$this->page = PageCache::getInstance()->getPage($this->pageID);
		if ($this->page === null) {
			throw new IllegalLinkException();
		}
	}

	protected function initObjectList() {
		parent::initObjectList();

		$this->objectList->setPage($this->page);
	}

	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'page' => $this->page
		));
	}
}
