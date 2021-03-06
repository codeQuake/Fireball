<?php
namespace cms\acp\form;

use cms\data\stylesheet\StylesheetAction;
use wcf\acp\form\AbstractAcpForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the stylesheet add form.
 * 
 * @author	Jens Krumsieck, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetAddForm extends AbstractAcpForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.stylesheet.add';

	/**
	 * scss
	 * @var	string
	 */
	public $scss = '';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.style.canAddStylesheet'];

	/**
	 * title of the stylesheet
	 * @var	string
	 */
	public $title = '';

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['scss'])) $this->scss = StringUtil::trim($_POST['scss']);
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

		// validate title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}

		// validate scss
		if (empty($_POST['scss'])) {
			throw new UserInputException('scss');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$data = [
			'title' => $this->title,
			'scss' => $this->scss
		];

		$this->objectAction = new StylesheetAction([], 'create', [
			'data' => $data
		]);
		$this->objectAction->executeAction();
		
		$this->reset();
	}
	
	/**
	 * @inheritDoc
	 */
	public function reset() {
		parent::reset();
		
		// reset variables
		$this->title = $this->scss = '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'title' => $this->title,
			'scss' => $this->scss
		]);
	}
}
