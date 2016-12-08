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
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
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
	 * @see	\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'title';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.fireball.file.canAddFile');

	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array('downloads', 'fileID', 'filesize', 'title', 'uploadTime');

	/**
	 * @see	\wcf\page\IPage::readParameters()
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
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// read categories
		$categoryNodeTree = new CategoryNodeTree('de.codequake.cms.file', 0, true);
		$this->categoryList = $categoryNodeTree->getIterator();
	}

	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		$this->objectList = new CategoryFileList(array($this->categoryID));
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.file'))
		));
	}
}
