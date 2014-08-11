<?php
namespace cms\acp\page;

use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\PageCache;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows a list of contents
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentListPage extends AbstractPage {
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

	public $pageID = 0;

	public $page = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		else throw new IllegalLinkException();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$this->page = PageCache::getInstance()->getPage($this->pageID);
		$this->contentListBody = new DrainedPositionContentNodeTree(null, $this->pageID, null, 'body', 1);
		$this->contentListSidebar = new DrainedPositionContentNodeTree(null, $this->pageID, null, 'sidebar', 1);
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');

	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'contentListBody' => $this->contentListBody->getIterator(),
			'contentListSidebar' => $this->contentListSidebar->getIterator(),
			'objectTypeList' => $this->objectTypeList,
			'page' => $this->page
		));
	}
}
