<?php
namespace cms\acp\page;

use cms\data\file\FileList;
use wcf\data\category\CategoryNodeTree;
use wcf\page\MultipleLinkPage;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows a list of files in a specific category.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileListPage extends MultipleLinkPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.file.list';

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
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.file.canAddFile');

	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'cms\data\file\FileList';

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
		parent::initObjectList();

		if ($this->categoryID) {
			$this->objectList->getConditionBuilder()->add('categoryID = ?', array($this->categoryID));
		} else {
			$this->objectList->getConditionBuilder()->add('categoryID IS NULL');
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList
		));
	}
}
