<?php
namespace cms\acp\form;

use cms\data\file\FileList;
use cms\data\folder\Folder;
use cms\data\folder\FolderAction;
use cms\data\folder\FolderList;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the file management form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileManagementForm extends AbstractForm {

	public $templateName = 'fileManagement';

	public $neededPermissions = array(
		'admin.cms.file.canAddFile'
	);

	public $activeMenuItem = 'cms.acp.menu.link.cms.file.management';

	public $folderPageID = 0;

	public $folderID = 0;

	public $fileList = null;

	public $folderList = null;

	public $foldername = '';

	public $isFolder = false;

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['folder'])) $this->foldername = StringUtil::trim($_POST['folder']);
	}

	public function validate() {
		parent::validate();
		if (empty($this->foldername)) {
			throw new UserInputException('folder', 'empty');
		}
		
		$folderPath = StringUtil::firstCharToLowerCase($this->foldername);
		if (file_exists(CMS_DIR . 'files/' . $folderPath)) throw new UserInputException('folder', 'exists');
	
	}

	public function save() {
		parent::save();
		$folderPath = StringUtil::firstCharToLowerCase($this->foldername);
		mkdir(CMS_DIR . 'files/' . $folderPath, 0777);
		$data = array(
			'folderName' => $this->foldername,
			'folderPath' => $folderPath
		);
		$action = new FolderAction(array(), 'create', array(
			'data' => $data
		));
		$action->executeAction();
		
		$this->saved();
		WCF::getTPL()->assign('success', true);
		
		$this->foldername = null;
	
	}

	public function readData() {
		parent::readData();
		if (isset($_REQUEST['id'])) $this->folderPageID = intval($_REQUEST['id']);
		if ($this->folderPageID == 0) {
			$list = new FileList();
			// get root files
			$list->getConditionBuilder()->add('folderID = ?', array(
				0
			));
			$list->readObjects();
			$this->fileList = $list->getObjects();
			$this->isFolder = false;
		} else {
			$folder = new Folder($this->folderPageID);
			if ($folder === null) throw new IllegalLinkException();
			$this->fileList = $folder->getFiles();
			$this->isFolder = true;
		}
		$folders = new FolderList();
		$folders->readObjects();
		$this->folders = $folders->getObjects();
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'fileList' => $this->fileList,
			'folderID' => $this->folderPageID,
			'folderList' => $this->folders,
			'foldername' => $this->foldername,
			'isFolder' => $this->isFolder
		));
	}
}
