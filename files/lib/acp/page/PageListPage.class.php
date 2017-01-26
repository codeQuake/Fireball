<?php
namespace cms\acp\page;

use cms\data\page\PageNodeTree;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Shows a list of pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageListPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.list';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.page.canListPage'];

	/**
	 * list of content types
	 * @var	array<\wcf\data\object\type\ObjectType>
	 */
	public $objectTypeList = null;

	/**
	 * list of pages
	 * @var	\RecursiveIteratorIterator
	 */
	public $pageList = null;

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');

		// read pages
		$pageNodeTree = new PageNodeTree();
		$this->pageList = $pageNodeTree->getIterator();
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.page')),
			'objectTypeList' => $this->objectTypeList,
			'pageList' => $this->pageList
		]);
	}
}
