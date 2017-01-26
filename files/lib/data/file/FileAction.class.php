<?php
namespace cms\data\file;

use cms\system\cache\builder\FileCacheBuilder;
use wcf\data\category\CategoryNodeTree;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\acl\ACLHandler;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\image\ImageHandler;
use wcf\system\upload\UploadFile;
use wcf\system\WCF;
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
	protected $className = FileEditor::class;

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = ['admin.fireball.file.canAddFile'];

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = ['admin.fireball.file.canAddFile'];

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = ['delete'];

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = ['getFilePreview'];

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

		return [
			'fileID' => $file->fileID,
			'template' => WCF::getTPL()->fetch('fileDetails', 'cms', [
				'file' => $file,
				'fileACLObjectTypeID' => ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.file')
			]),
			'title' => $file->getTitle()
		];
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
		$allowedTypes = ['code', 'film', 'image', 'music', 'pdf'];
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
		$fileList = new CategoryFileList([$category->categoryID]);
		$fileList->sqlOrderBy = 'title ASC';
		if ($this->parameters['fileType']) {
			$fileList->getConditionBuilder()->add('file.fileType LIKE ?', [$this->parameters['fileType'].'%']);
		}
		$fileList->readObjects();

		// output
		WCF::getTPL()->assign([
			'category' => $category,
			'fileList' => $fileList
		]);

		if ($this->parameters['categoryID']) {
			// category specified => only return formatted list of
			// files.
			return [
				'categoryID' => $category->categoryID,
				'template' => WCF::getTPL()->fetch('categoryFileListDialog', 'cms')
			];
		} else {
			// category wasn't specified => return markup for a
			// complete dialog, not only the formatted file list.
			$categoryNodeTree = new CategoryNodeTree('de.codequake.cms.file', 0, true);
			$this->categoryList = $categoryNodeTree->getIterator();

			WCF::getTPL()->assign([
				'categoryList' => $this->categoryList
			]);

			return [
				'categoryID' => $category->categoryID,
				'template' => WCF::getTPL()->fetch('fileListDialog', 'cms'),
				'title' => WCF::getLanguage()->get('cms.acp.file.picker')
			];
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

		WCF::getTPL()->assign([
			'file' => $file
		]);

		return [
			'template' => WCF::getTPL()->fetch('filePreview', 'cms'),
			'fileID' => $file->fileID
		];
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
			return [
				'width' => $size[0],
				'height' => $size[1]
			];
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

		WCF::getTPL()->assign([
			'categoryList' => $categoryList
		]);

		return [
			'template' => WCF::getTPL()->fetch('fileUploadDialog', 'cms'),
			'title' => WCF::getLanguage()->get('cms.acp.file.add')
		];
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
		$failedUploads = [];
		$result = ['files' => [], 'errors' => []];

		/** @var UploadFile $file */
		foreach ($files as $file) {
			try {
				if ($file->getValidationErrorType()) {
					$failedUploads[] = $file;
					continue;
				}

				$data = [
					'title' => $file->getFilename(),
					'filesize' => $file->getFilesize(),
					'fileType' => $file->getMimeType(),
					'fileHash' => sha1_file($file->getLocation()),
					'uploadTime' => TIME_NOW
				];

				$imageData = $file->getImageData();
				if (!empty($imageData)) {
					$data['width'] = $imageData['width'];
					$data['height'] = $imageData['height'];
				}

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

					// generate thumbnails
					if (in_array($uploadedFile->fileType, File::$thumbnailMimeTypes)) {
						$thumbnailAction = new self([$uploadedFile], 'generateThumbnail');
						$thumbnailAction->executeAction();
					}

					$result['files'][$file->getInternalFileID()] = [
						'fileID' => $uploadedFile->fileID,
						'title' => $uploadedFile->getTitle(),
						'filesize' => $uploadedFile->filesize,
						'formattedFilesize' => FileUtil::formatFilesize($uploadedFile->filesize)
					];
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
			$result['errors'][$failedUpload->getInternalFileID()] = [
				'title' => $failedUpload->getFilename(),
				'filesize' => $failedUpload->getFilesize(),
				'errorType' => $failedUpload->getValidationErrorType()
			];
		}

		return $result;
	}

	/**
	 * Validates the generate thumbnail action
	 * @throws UserInputException
	 */
	public function validateGenerateThumbnail() {
		/** @var File $object */
		foreach ($this->objects as $object) {
			if (!in_array($object->fileType, File::$thumbnailMimeTypes)) {
				throw new UserInputException('objectIDs');
			}
		}
	}

	/**
	 * Generates a thumbnail with max 500x500px fot the given objects
	 * @throws \wcf\system\exception\SystemException
	 */
	public function generateThumbnail() {
		$minWidth = 500;
		$minHeight = 500;

		$thumbnailWidth = 500;
		$thumbnailHeight = 500;

		/** @var FileEditor $object */
		foreach ($this->objects as $fileEditor) {
			/** @var File $file */
			$file = $fileEditor->getDecoratedObject();

			// check memory limit
			if (!FileUtil::checkMemoryLimit($file->width * $file->height * ($file->fileType == 'image/png' ? 4 : 3) * 2.1)) {
				return;
			}

			$adapter = ImageHandler::getInstance()->getAdapter();
			$adapter->loadFile($file->getLocation());

			$updateData = [];

			$thumbnailLocation = $file->getThumbnailLocation();
			@unlink($thumbnailLocation);

			if ($file->width > $minWidth || $file->height > $minHeight) {
				$thumbnail = $adapter->createThumbnail($thumbnailWidth, $thumbnailHeight);
				$adapter->writeImage($thumbnail, $thumbnailLocation);
				if (file_exists($thumbnailLocation) && ($imageData = @getimagesize($thumbnailLocation)) !== false) {
					$updateData['fileTypeThumbnail'] = $imageData['mime'];
					$updateData['filesizeThumbnail'] = @filesize($thumbnailLocation);
					$updateData['widthThumbnail'] = $imageData[0];
					$updateData['heightThumbnail'] = $imageData[1];
				}
			}

			if (!empty($updateData)) {
				$fileEditor->update($updateData);
			}
		}
	}
}
