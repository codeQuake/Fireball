<?php
namespace cms\acp\form;

use cms\data\stylesheet\Stylesheet;
use cms\data\stylesheet\StylesheetAction;
use cms\data\stylesheet\StylesheetList;
use wcf\acp\form\AbstractAcpForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the stylesheet edit form.
 * 
 * @author	Jens Krumsieck, Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetEditForm extends StylesheetAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.stylesheet';

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
	 * list of available stylesheets
	 * @var	array<\cms\data\stylesheet\Stylesheet>
	 */
	public $stylesheets = [];

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->stylesheetID = intval($_REQUEST['id']);
		$this->stylesheet = new Stylesheet($this->stylesheetID);
		if (!$this->stylesheet->stylesheetID) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractAcpForm::save();

		$data = [
			'title' => $this->title,
			'scss' => $this->scss
		];

		$this->objectAction = new StylesheetAction([$this->stylesheet], 'update', [
			'data' => $data
		]);
		$this->objectAction->executeAction();

		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		$stylesheetList = new StylesheetList();
		$stylesheetList->sqlOrderBy = 'stylesheet.title ASC';
		$stylesheetList->readObjects();
		$this->stylesheets = $stylesheetList->getObjects();

		if (empty($_POST)) {
			$this->title = $this->stylesheet->title;
			$this->scss = $this->stylesheet->scss;

			// backward compatibility to maelstrom & typhoon
			if (empty($this->scss) && !empty($this->stylesheet->less)) {
				$this->scss = $this->stylesheet->less;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'action' => 'edit',
			'stylesheetID' => $this->stylesheetID,
			'stylesheets' => $this->stylesheets
		]);
	}
}
