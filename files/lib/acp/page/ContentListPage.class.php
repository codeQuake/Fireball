<?php
namespace cms\acp\page;

use cms\data\content\ContentNodeTree;
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
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';
	public $neededPermissions = array(
		'admin.cms.page.canListPage'
	);
	public $templateName = 'contentList';
	public $pageList = null;
	public $objectTypeList = null;

	public $pageID = 0;
	public $page = null;

	public function readParameters() {
		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		else throw new IllegalLinkException();
	}

	public function readData() {
		parent::readData();
		$this->page = PageCache::getInstance()->getPage($this->pageID);
		$this->contentList = new ContentNodeTree(null, $this->pageID);
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');

	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
		'contentListBody' => $this->contentList->getIterator(),
		'contentListSidebar' => $this->contentList->getIterator(),
		'objectTypeList' => $this->objectTypeList,
		'page' => $this->page
		));
	}
}
