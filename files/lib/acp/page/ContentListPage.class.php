<?php
namespace cms\acp\page;

use cms\data\content\PageContentList;
use cms\data\page\Page;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ContentListPage extends SortablePage {
	
	// cause it will show the pagelist when no id is given
	public $objectListClassName = 'cms\data\page\PageList';
	public $activeMenuItem = 'cms.acp.menu.link.cms.content.list';
	public $neededPermissions = array(
		'admin.cms.content.canListContent'
	);
	public $templateName = 'contentList';
	public $defaultSortfield = 'contentID';
	public $validSortFields = array(
		'contentID',
		'title'
	);
	public $objectList = array();
	public $pageID = 0;

	public function readParameters() {
		parent::readParameters();
		
		if (isset($_GET['id'])) $this->pageID = intval($_GET['id']);
	}

	public function initObjectList() {
		parent::initObjectList();
		if ($this->pageID != 0) {
			$this->objectList = new PageContentList($this->pageID);
		}
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'pageID' => $this->pageID,
			'page' => new Page($this->pageID)
		));
	}
}
