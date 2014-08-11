<?php
namespace cms\acp\form;

use cms\system\backup\BackupHandler;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 * Handles import of an uploaded backup.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSImportForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.import';

	/**
	 * uploaded file
	 * @var	array<mixed>
	 */
	public $file = null;

	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'import';

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_FILES['file'])) $this->file = $_FILES['file'];
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		// check if file is given
		if (empty($this->file)) {
			throw new UserInputException('file', 'empty');
		}
		if (empty($this->file['tmp_name'])) {
			throw new UserInputException('file', 'empty');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		BackupHandler::getInstance()->handleImport($this->file['tmp_name']);

		$this->saved();
		WCF::getTPL()->assign('success', true);
	}
}
