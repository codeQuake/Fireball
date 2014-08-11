<?php
namespace cms\acp\form;

use cms\data\layout\Layout;
use cms\data\layout\LayoutAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the layout edit form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LayoutEditForm extends LayoutAddForm {
	/**
	 * layout id
	 * @var	integer
	 */
	public $layoutID = 0;

	/**
	 * layout object
	 * @var	\cms\data\layout\Layout
	 */
	public $layout = null;

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_REQUEST['id'])) $this->layoutID = intval($_REQUEST['id']);
		if (isset($_POST['data'])) $this->data = $_POST['data'];
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();

		$objectAction = new LayoutAction(array(
			$this->layoutID
		), 'update', array(
			'data' => array(
				'title' => $this->title,
				'data' => serialize($this->data)
			)
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

		if (isset($_REQUEST['id'])) $this->layoutID = intval($_REQUEST['id']);
		$this->layout = new Layout($this->layoutID);
		$this->title = $this->layout->title;
		$this->data = @unserialize($this->layout->data);
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'layoutID' => $this->layoutID
		));
	}
}
