<?php
namespace cms\acp\form;

use cms\system\backup\BackupHandler;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 * handling imports
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class CMSImportForm extends AbstractForm {

	public $templateName = 'import';
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.import';
	public $file = null;

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_FILES['file'])) $this->file = $_FILES['file'];
	}

	public function validate() {
		parent::validate();
		// check if file is given
		if (empty($this->file)) {
			throw new UserInputException('file', 'empty');
		}
		if (empty($this->file['tmp_name'])) throw new UserInputException('file', 'empty');
	}

	public function save() {
		parent::save();
		BackupHandler::getInstance()->handleImport($this->file['tmp_name']);
		$this->saved();
		WCF::getTPL()->assign('success', true);
	}

}
