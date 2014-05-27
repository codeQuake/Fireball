<?php
namespace cms\data\file;

use cms\data\folder\Folder;
use cms\data\folder\FolderList;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Executes file-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileAction extends AbstractDatabaseObjectAction {

	protected $className = 'cms\data\file\FileEditor';

	protected $permissionsDelete = array(
		'admin.cms.file.canAddFile'
	);

	protected $requireACP = array(
		'delete'
	);

	public function delete() {
		// del files
		foreach ($this->objectIDs as $objectID) {
			$file = new File($objectID);
			if ($file->folderID == 0 && file_exists(CMS_DIR . 'files/' . $file->filename)) unlink(CMS_DIR . 'files/' . $file->filename);
			else {
				$folder = new Folder($file->folderID);
				if (file_exists(CMS_DIR . 'files/' . $folder->folderPath . '/' . $file->filename)) unlink(CMS_DIR . 'files/' . $folder->folderPath . '/' . $file->filename);
			}
		}
		parent::delete();
	}

	public function validateGetImages() {
		// does nothing like a boss
	}

	public function getImages() {
		if ($this->parameters['imageID']) {
			$image = new File($this->parameters['imageID']);
			if ($image->imageID) {
				WCF::getTPL()->assign('image', $image);
			}
		}

		// file images
		$list = new FileList();
		// get images only
		$list->getConditionBuilder()->add('file.type LIKE ?', array(
			'image/%'
		));

		// main folder
		$list->getConditionBuilder()->add('file.folderID =  ?', array(
			'0'
		));
		$list->readObjects();
		$imageList = $list->getObjects();

		$list = new FolderList();
		$list->readObjects();
		$folderList = $list->getObjects();

		WCF::getTPL()->assign(array(
			'images' => $imageList,
			'folders' => $folderList
		));

		return array(
			'images' => $imageList,
			'template' => WCF::getTPL()->fetch('imageContentList', 'cms')
		);
	}

	public function validateUpload() {
		if (count($this->parameters['__files']->getFiles()) <= 0) {
			throw new UserInputException('files');
		}
	}

	public function upload() {
		// get file
		$files = $this->parameters['__files']->getFiles();
		$return = array();
		foreach ($files as $file) {
			try {

				if (! $file->getValidationErrorType()) {
					$filename = 'FB-File-' . md5($file->getFilename() . time()) . '.' . $file->getFileExtension();
					$folderID = $this->parameters['folderID'];
					if ($folderID != 0) $folder = new Folder($folderID);
					$data = array(
						'title' => $file->getFilename(),
						'folderID' => $folderID,
						'filename' => $filename,
						'size' => $file->getFilesize(),
						'type' => $file->getMimeType()
					);

					$uploadedFile = FileEditor::create($data);
					if ($folderID == 0) $path = CMS_DIR . 'files/' . $filename;
					else $path = CMS_DIR . 'files/' . $folder->folderPath . '/' . $filename;
					if (@move_uploaded_file($file->getLocation(), $path)) {
						@unlink($file->getLocation());

						$return[] = array(
							'fileID' => $uploadedFile->fileID,
							'folderID' => $uploadedFile->folderID,
							'title' => $uploadedFile->title,
							'filename' => $uploadedFile->filename,
							'filesize' => $uploadedFile->filesize,
							'formattedFilesize' => FileUtil::formatFilesize($uploadedFile->filesize)
						);
					} else {
						// failure
						$editor = new FileEditor($uploadedFile);
						$editor->delete();
						throw new UserInputException('file', 'uploadFailed');
					}
				}
			} catch (UserInputException $e) {
				$file->setValidationErrorType($e->getType());
			}
		}

		return $return;
	}
}
