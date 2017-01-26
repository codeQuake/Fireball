<?php
namespace cms\acp\form;

use cms\system\backup\BackupHandler;
use wcf\form\AbstractForm;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\StringUtil;

/**
 * Handles import of an uploaded backup.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSImportForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.importAndExport';

	/**
	 * link to backup file
	 * @var	string
	 */
	public $backup = '';
	
	/**
	 * uploaded file
	 * @var	array<mixed>
	 */
	public $file = null;
	
	/**
	 * linked file
	 * @var	string
	 */
	public $fileLink = '';

	/**
	 * @inheritDoc
	 */
	public $templateName = 'import';

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_FILES['file'])) $this->file = $_FILES['file'];
		if (isset($_POST['fileLink'])) $this->fileLink = StringUtil::trim($_POST['fileLink']);
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

		// check if file is uploaded or linked
		if (!empty($this->file['tmp_name'])) $this->backup = $this->file['tmp_name'];
		else if ($this->fileLink != '') {
			//check if file is external url
			if (FileUtil::isURL($this->fileLink)) {
				try {
					//download file
					$this->backup = FileUtil::downloadFileFromHttp($this->fileLink, 'cms_backup');
				}
				catch (SystemException $e) {
					//download failed
					throw new UserInputException('fileLink', 'downloadFailed');
				}
			} 
			//file is on same server or invalid
			else {
				//file not found
				if (!file_exists($this->fileLink)) throw new UserInputException('fileLink', 'notFound');
				//file found
				else $this->backup = $this->fileLink;
			}
		}
		else throw new UserInputException('file', 'empty');
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		//perform import
		BackupHandler::getInstance()->handleImport($this->backup);

		$this->saved();
		WCF::getTPL()->assign('success', true);
	}
}
