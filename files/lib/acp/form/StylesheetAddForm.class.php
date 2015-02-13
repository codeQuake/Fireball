<?php
namespace cms\acp\form;

use cms\data\stylesheet\StylesheetAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the stylesheet add form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.stylesheet.add';

	/**
	 * less
	 * @var	string
	 */
	public $less = '';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.style.canAddStylesheet');

	/**
	 * title of the stylesheet
	 * @var	string
	 */
	public $title = '';

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['less'])) $this->less = StringUtil::trim($_POST['less']);
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$data = array(
			'title' => $this->title,
			'less' => $this->less
		);

		$this->objectAction = new StylesheetAction(array(), 'create', array(
			'data' => $data
		));
		$this->objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);

		$this->title = $this->less = '';
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'title' => $this->title,
			'less' => $this->less
		));
	}
}
