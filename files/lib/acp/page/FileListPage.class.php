<?php
namespace cms\acp\page;

use cms\data\file\CategoryFileList;
use wcf\data\category\CategoryNodeTree;
use wcf\page\SortablePage;
use wcf\system\category\CategoryHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows a list of files in a specific category.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.file.list';

	/**
	 * category id
	 * @var	integer
	 */
	public $categoryID = 0;

	/**
	 * category object
	 * @var	\wcf\data\category\Category
	 */
	public $category = null;

	/**
	 * list of categories
	 * @var	\RecursiveIteratorIterator
	 */
	public $categoryList = null;

	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'title';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.file.canAddFile'];

	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['downloads', 'fileID', 'filesize', 'title', 'uploadTime'];

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) {
			$this->categoryID = intval($_REQUEST['id']);
			$this->category = CategoryHandler::getInstance()->getCategory($this->categoryID);
			if ($this->category === null) {
				throw new IllegalLinkException();
			}
		} else {
			// load first category
			$categories = CategoryHandler::getInstance()->getCategories('de.codequake.cms.file');
			$this->category = array_shift($categories);
			if ($this->category !== null)
				$this->categoryID = $this->category->categoryID;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// read categories
		$categoryNodeTree = new CategoryNodeTree('de.codequake.cms.file', 0, true);
		$this->categoryList = $categoryNodeTree->getIterator();
	}

	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		$this->objectList = new CategoryFileList([$this->categoryID]);
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.file'))
		]);
	}
}
