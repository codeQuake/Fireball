<?php
namespace cms\acp\page;

use cms\data\content\DrainedPositionContentNodeTree;
use cms\data\page\PageCache;
use cms\data\page\PageNodeTree;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows a list of contents.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentListPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.list';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.page.canListPage'];

	/**
	 * list of pages
	 * @var	\RecursiveIteratorIterator
	 */
	public $pageList = null;
	
	/**
	 * @var DrainedPositionContentNodeTree[]
	 */
	public $contentList = [];

	/**
	 * list of content types
	 * @var	array<\wcf\data\object\type\ObjectType>
	 */
	public $objectTypeList = null;

	/**
	 * id of the selected page
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * object of the selected page
	 * @var	\cms\data\page\Page
	 */
	public $page = null;

	/**
	 * available content positions
	 * @var string[]
	 */
	protected $availablePositions = ['hero', 'headerBoxes', 'top', 'sidebarLeft', 'body', 'sidebarRight', 'bottom', 'footerBoxes', 'footer'];

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->pageID = intval($_REQUEST['id']);
		$this->page = PageCache::getInstance()->getPage($this->pageID);
		if ($this->page === null) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		$pageNodeTree = new PageNodeTree();
		$this->pageList = $pageNodeTree->getIterator();

		foreach ($this->availablePositions as $position) {
			$this->contentList[$position] = new DrainedPositionContentNodeTree(null, $this->pageID, null, $position, 1);
		}
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.content.type');
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'contentList' => $this->contentList,
			'availablePositions' => $this->availablePositions,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.content')),
			'objectTypeList' => $this->objectTypeList,
			'pageID' => $this->pageID,
			'page' => $this->page,
			'pageList' => $this->pageList
		]);
	}
}
