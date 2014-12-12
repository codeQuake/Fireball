<?php
namespace cms\data\file;

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
 * @copyright	2014 codeQuake
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
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete');

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

		// validate type
		$this->readString('type', true);
		$allowedTypes = array('code', 'film', 'music', 'pdf', 'picture');
		if ($this->parameters['type'] && !in_array($this->parameters['type'], $allowedTypes)) {
			throw new UserInputException('type');
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
		if ($this->parameters['type']) {
			$fileList->getConditionBuilder()->add('file.type LIKE ?', array($this->parameters['type'].'%'));
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
				'template' => WCF::getTPL()->fetch('fileListDialog', 'cms')
			);
		}
	}

	/**
	 * Validates parameters to upload a file.
	 */
	public function validateUpload() {
		if (count($this->parameters['__files']->getFiles()) <= 0) {
			throw new UserInputException('files');
		}

		// validate category
		if (!isset($this->parameters['categoryIDs'])) {
			throw new UserInputException('categoryIDs');
		}

		// we receive the category ids as string due to the convertion
		// of javascript's FormData::append()
		$this->parameters['categoryIDs'] = explode(',', $this->parameters['categoryIDs']);
		$this->parameters['categoryIDs'] = ArrayUtil::toIntegerArray($this->parameters['categoryIDs']);

		foreach ($this->parameters['categoryIDs'] as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) {
				throw new UserInputException('categoryIDs');
			}
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
				$uploadedFileEditor = new FileEditor($uploadedFile);
				$uploadedFileEditor->updateCategoryIDs($this->parameters['categoryIDs']);

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
						'filesize' => $uploadedFile->filesize,
						'formattedFilesize' => FileUtil::formatFilesize($uploadedFile->filesize)
					);
				} else {
					// failure
					$uploadedFileEditor->delete();

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
