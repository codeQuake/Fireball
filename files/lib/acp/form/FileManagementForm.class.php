<?php
namespace cms\acp\form;

use cms\data\file\FileAction;
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
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class FileManagementForm extends AbstractForm {
	public $templateName = 'fileAdd';
	public $neededPermissions = array(
		'admin.cms.file.canAddFile'
	);
	public $activeMenuItem = 'cms.acp.menu.link.cms.file.management';
	public $folderPageID = 0;
	public $folderID = 0;
	public $file = null;
	public $fileList = null;
	public $folderList = null;
	public $foldername = '';
	public $isFolder = false;

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_GET['action'])) $this->action = StringUtil::trim($_GET['action']);
		if ($this->action == 'file') {
			if (isset($_FILES['file'])) $this->file = $_FILES['file'];
			if (isset($_POST['folderID'])) $this->folderID = intval($_POST['folderID']);
		}
		if ($this->action == 'folder') {
			if (isset($_POST['folder'])) $this->foldername = StringUtil::trim($_POST['folder']);
		}
	}

	public function validate() {
		parent::validate();
		if ($this->action == 'file') {
			// check if file is given
			if (empty($this->file)) {
				throw new UserInputException('file', 'empty');
			}
			if (empty($this->file['tmp_name'])) throw new UserInputException('file', 'empty');
			if ($this->file['size'] >= $this->return_bytes(ini_get('upload_max_filesize'))) throw new UserInputException('file', 'tooBig');
			if ($this->file['size'] >= $this->return_bytes(ini_get('post_max_size'))) throw new UserInputException('file', 'tooBig');
			$allowedTypes = ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('admin.cms.file.allowedTypes')));
			$tmp = explode('.', $this->file['name']);
			$fileType = array_pop($tmp);
			if (! in_array($fileType, $allowedTypes)) throw new UserInputException('file', 'invalid');
		}
		if ($this->action == 'folder') {
			if (empty($this->foldername)) {
				throw new UserInputException('folder', 'empty');
			}
			
			$folderPath = StringUtil::firstCharToLowerCase($this->foldername);
			if (file_exists(CMS_DIR . 'files/' . $folderPath)) throw new UserInputException('folder', 'exists');
		}
	}

	public function save() {
		parent::save();
		if ($this->action == 'file') {
			$tmp = explode('.', $this->file['name']);
			$filename = 'FB-File-' . md5($this->file['tmp_name'] . time()) . '.' . array_pop($tmp);
			$folder = new Folder($this->folderID);
			$path = CMS_DIR . 'files/' . $folder->folderPath . '/' . $filename;
			move_uploaded_file($this->file['tmp_name'], $path);
			
			$data = array(
				'title' => $this->file['name'],
				'folderID' => $this->folderID,
				'filename' => $filename,
				'type' => $this->file['type'],
				'size' => $this->file['size']
			);
			$action = new FileAction(array(), 'create', array(
				'data' => $data
			));
			$action->executeAction();
			
			$this->saved();
			WCF::getTPL()->assign('success', true);
			
			$this->file = null;
		}
		if ($this->action == 'folder') {
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
		}
		else {
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
			'folderID' => $this->folderID,
			'folderList' => $this->folders,
			'foldername' => $this->foldername,
			'isFolder' => $this->isFolder,
			'maxSize' => $this->return_bytes(ini_get('upload_max_filesize'))
		));
	}
	
	// see http://www.php.net/manual/de/function.ini-get.php
	protected function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		switch ($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		
		return $val;
	}
}
