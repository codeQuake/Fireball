<?php
namespace cms\acp\form;

use cms\data\layout\LayoutAction;
use cms\data\stylesheet\StylesheetList;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the layout add form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LayoutAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.layout.add';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.style.canAddLayout');

	/**
	 * layout title
	 * @var	string
	 */
	public $title = '';

	public $data = array();

	/**
	 * list of stylesheets
	 * @var	\cms\data\stylesheet\StylesheetList
	 */
	public $stylesheetList = null;

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['data'])) $this->data = $_POST['data'];
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		if (empty($this->data)) {
			throw new UserInputException('data');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$objectAction = new LayoutAction(array(), 'create', array(
			'data' => array(
				'title' => $this->title,
				'data' => serialize($this->data)
			)
		));
		$objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);

		// reset values
		$this->title = '';
		$this->data = array();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$this->stylesheetList = new StylesheetList();
		$this->stylesheetList->readObjects();
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'title' => $this->title,
			'data' => $this->data,
			'action' => 'add',
			'sheetList' => $this->stylesheetList
		));
	}
}
