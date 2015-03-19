<?php
namespace cms\data\file;

use cms\data\file\FileCache;
use cms\system\cache\builder\FileCacheBuilder;
use wcf\data\category\CategoryNodeTree;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\FileUtil;

/**
 * Executes file-related actions.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\file\FileEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.file.canAddFile');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.cms.file.canAddFile');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = array('getFilePreview');

	/**
	 * Validate parameters and permissions to fetch details about a file.
	 */
	public function validateGetDetails() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Returns a formatted details view of a file.
	 */
	public function getDetails() {
		$file = $this->getSingleObject();

		WCF::getTPL()->assign(array(
			'file' => $file
		));

		return array(
			'fileID' => $file->fileID,
			'template' => WCF::getTPL()->fetch('fileDetails', 'cms'),
			'title' => $file->getTitle()
		);
	}

	/**
	 * Validates parameters and permissions to fetch a rendered list of
	 * files.
	 */
	public function validateGetFileList() {
		// validate category
		$this->readInteger('categoryID', true);
		if ($this->parameters['categoryID']) {
			$category = CategoryHandler::getInstance()->getCategory($this->parameters['categoryID']);
			if ($category === null) {
				throw new UserInputException('categoryID');
			}
		}

		// validate file type
		$this->readString('fileType', true);
		$allowedTypes = array('code', 'film', 'image', 'music', 'pdf');
		if ($this->parameters['fileType'] && !in_array($this->parameters['fileType'], $allowedTypes)) {
			throw new UserInputException('fileType');
		}
	}

	/**
	 * Returns a formatted list of files. In case no category is specified,
	 * the complete markup for a dialog is returned, otherwise only a
	 * rendered list of files.
	 */
	public function getFileList() {
		// load category
		if ($this->parameters['categoryID']) {
			$category = CategoryHandler::getInstance()->getCategory($this->parameters['categoryID']);
		} else {
			// load first category
			$categories = CategoryHandler::getInstance()->getCategories('de.codequake.cms.file');
			$category = array_shift($categories);
		}

		// load files assigned to the category
		$fileList = new CategoryFileList(array($category->categoryID));
		$fileList->sqlOrderBy = 'title ASC';
		if ($this->parameters['fileType']) {
			$fileList->getConditionBuilder()->add('file.fileType LIKE ?', array($this->parameters['fileType'].'%'));
		}
		$fileList->readObjects();

		// output
		WCF::getTPL()->assign(array(
			'category' => $category,
			'fileList' => $fileList
		));

		if ($this->parameters['categoryID']) {
			// category specified => only return formatted list of
			// files.
			return array(
				'categoryID' => $category->categoryID,
				'template' => WCF::getTPL()->fetch('categoryFileListDialog', 'cms')
			);
		} else {
			// category wasn't specified => return markup for a
			// complete dialog, not only the formatted file list.
			$categoryNodeTree = new CategoryNodeTree('de.codequake.cms.file', 0, true);
			$this->categoryList = $categoryNodeTree->getIterator();

			WCF::getTPL()->assign(array(
				'categoryList' => $this->categoryList
			));

			return array(
				'categoryID' => $category->categoryID,
				'template' => WCF::getTPL()->fetch('fileListDialog', 'cms'),
				'title' => WCF::getLanguage()->get('cms.acp.file.picker')
			);
		}
	}

	/**
	 * Validate parameters and permissions to get a preview of a file.
	 */
	public function validateGetFilePreview() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * WCF.Popover implementation for files
	 */
	public function getFilePreview() {
		$file = $this->getSingleObject();

		WCF::getTPL()->assign(array(
			'file' => $file
		));

		return array(
			'template' => WCF::getTPL()->fetch('filePreview', 'cms'),
			'fileID' => $file->fileID
		);
	}

	/**
	 * Validate parameters and permissions to fetch size information about a file.
	 */
	public function validateGetSize() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Returns size information about a file.
	 */
	public function getSize() {
		$file = $this->getSingleObject();
		$size = $file->getImageSize();

		if ($file->isImage()) {
			return array(
				'width' => $size[0],
				'height' => $size[1]
			);
		}

		return false;
	}

	/**
	 * Validates parameters and permissions to get a upload dialog.
	 */
	public function validateGetUploadDialog() {
		// validate category
		$this->readInteger('categoryID', true);
		if ($this->parameters['categoryID']) {
			$category = CategoryHandler::getInstance()->getCategory($this->parameters['categoryID']);
			if ($category === null) {
				throw new UserInputException('categoryID');
			}
		}
	}

	/**
	 * Returns a formatted upload dialog.
	 */
	public function getUploadDialog() {
		$categoryNodeTree = new CategoryNodeTree('de.codequake.cms.file', 0, true);
		$categoryList = $categoryNodeTree->getIterator();

		WCF::getTPL()->assign(array(
			'categoryList' => $categoryList
		));

		return array(
			'template' => WCF::getTPL()->fetch('fileUploadDialog', 'cms'),
			'title' => WCF::getLanguage()->get('cms.acp.file.add')
		);
	}

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		parent::update();

		foreach ($this->objects as $fileEditor) {
			// update categories
			if (isset($this->parameters['categoryIDs'])) {
				$fileEditor->updateCategoryIDs($this->parameters['categoryIDs']);
			}
		}
	}

	/**
	 * Validates parameters to upload a file.
	 */
	public function validateUpload() {
		if (count($this->parameters['__files']->getFiles()) <= 0) {
			throw new UserInputException('files');
		}
	}

	/**
	 * Handles upload of a file.
	 */
	public function upload() {
		$files = $this->parameters['__files']->getFiles();
		$failedUploads = array();
		$result = array('files' => array(), 'errors' => array());

		foreach ($files as $file) {
			try {
				if ($file->getValidationErrorType()) {
					$failedUploads[] = $file;
					continue;
				}

				$data = array(
					'title' => $file->getFilename(),
					'filesize' => $file->getFilesize(),
					'fileType' => $file->getMimeType(),
					'fileHash' => sha1_file($file->getLocation()),
					'uploadTime' => TIME_NOW
				);

				$uploadedFile = FileEditor::create($data);
				
				//clear cache
				FileCacheBuilder::getInstance()->reset();

				// create subdirectory if necessary
				$dir = dirname($uploadedFile->getLocation());
				if (!@file_exists($dir)) {
					FileUtil::makePath($dir, 0777);
				}

				// move uploaded file
				if (@move_uploaded_file($file->getLocation(), $uploadedFile->getLocation())) {
					@unlink($file->getLocation());

					$result['files'][$file->getInternalFileID()] = array(
						'fileID' => $uploadedFile->fileID,
						'title' => $uploadedFile->getTitle(),
						'fileSize' => $uploadedFile->filesize,
						'formattedFilesize' => FileUtil::formatFilesize($uploadedFile->filesize)
					);
				} else {
					// failure
					$editor = new FileEditor($uploadedFile);
					$editor->delete();

					throw new UserInputException('file', 'uploadFailed');
				}
			}
			catch (UserInputException $e) {
				$file->setValidationErrorType($e->getType());
				$failedUploads[] = $file;
			}
		}

		// return results
		foreach ($failedUploads as $failedUpload) {
			$result['errors'][$failedUpload->getInternalFileID()] = array(
				'title' => $failedUpload->getFilename(),
				'filesize' => $failedUpload->getFilesize(),
				'errorType' => $failedUpload->getValidationErrorType()
			);
		}

		return $result;
	}
}
