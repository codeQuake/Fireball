<?php
namespace cms\acp\page;

use cms\data\page\PageNodeTree;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
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

	public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';

	public $neededPermissions = array(
		'admin.cms.page.canListPage'
	);

	public $templateName = 'pageList';

	public $pageList = null;

	public $objectTypeList = null;

	public function readData() {
		parent::readData();
		$this->pageList = new PageNodeTree(0);
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');
	
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'pageList' => $this->pageList->getIterator(),
			'objectTypeList' => $this->objectTypeList
		));
	}
}
