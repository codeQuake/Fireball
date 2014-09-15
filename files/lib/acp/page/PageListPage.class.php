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
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageListPage extends AbstractPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.page.canListPage');

	public $pageList = null;

	public $objectTypeList = null;

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$this->pageList = new PageNodeTree();
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');

	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.page')),
			'objectTypeList' => $this->objectTypeList,
			'pageList' => $this->pageList->getIterator()
		));
	}
}
