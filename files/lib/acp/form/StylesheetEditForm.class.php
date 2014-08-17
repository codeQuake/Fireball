<?php
namespace cms\acp\form;

use cms\data\stylesheet\Stylesheet;
use cms\data\stylesheet\StylesheetAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 * Shows the stylesheet edit form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetEditForm extends StylesheetAddForm {
	/**
	 * stylesheet id
	 * @var	integer
	 */
	public $stylesheetID = 0;

	/**
	 * stylesheet object
	 * @var	\cms\data\stylesheet\Stylesheet
	 */
	public $stylesheet = null;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->stylesheetID = intval($_REQUEST['id']);
		$this->stylesheet = new Stylesheet($this->stylesheetID);
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();

		$data = array(
			'title' => $this->title,
			'less' => $this->less
		);

		$objectAction = new StylesheetAction(array($this->stylesheet), 'update', array(
			'data' => $data
		));
		$objectAction->executeAction();

		$this->saved();
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		if (empty($_POST)) {
			$this->title = $this->stylesheet->title;
			$this->less = $this->stylesheet->less;
		}
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'stylesheetID' => $this->stylesheetID
		));
	}
}
