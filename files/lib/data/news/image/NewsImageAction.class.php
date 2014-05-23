<?php
namespace cms\data\news\image;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\upload\DefaultUploadFileValidationStrategy;
use wcf\system\WCF;

/**
 * Executes news image-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsImageAction extends AbstractDatabaseObjectAction {
	protected $className = 'cms\data\news\image\NewsImageEditor';
	protected $permissionsDelete = array(
		'admin.cms.news.canManageCategory'
	);
	protected $requireACP = array(
		'delete'
	);

	public function delete() {
		// del files
		foreach ($this->objectIDs as $objectID) {
			$file = new NewsImage($objectID);
			unlink(CMS_DIR . 'images/news/' . $file->filename);
		}
		parent::delete();
	}

	public function validateGetImages() {
		//does nothing
	}

	public function getImages() {
		if ($this->parameters['imageID']) {
			$image = new NewsImage($this->parameters['imageID']);
			if ($image->imageID) {
				WCF::getTPL()->assign('image', $image);
			}
		}

		// news images
		$list = new NewsImageList();
		$list->readObjects();
		$imageList = $list->getObjects();

		WCF::getTPL()->assign(array(
			'images' => $imageList
		));

		return array(
			'images' => $imageList,
			'template' => WCF::getTPL()->fetch('imageList', 'cms')
		);
	}

	public function validateUpload() {
		WCF::getSession()->checkPermissions(array('user.cms.news.canUploadAttachment'));

		if (count($this->parameters['__files']->getFiles()) != 1) {
			throw new UserInputException('files');
		}

		$this->parameters['__files']->validateFiles(new DefaultUploadFileValidationStrategy(WCF::getSession()->getPermission('user.attachment.maxSize'), explode("\n", WCF::getSession()->getPermission('user.cms.news.image.allowedExtensions'))));
	}

	public function upload() {
		//get file
		$files = $this->parameters['__files']->getFiles();
		$file = $files[0];
		try {

			if (!$file->getValidationErrorType()) {
				$filename = 'FB-File-' . md5($file->getFilename() . time()) . '.' . $file->getFileExtension();
				$data = array(
					'title' => $file->getFilename(),
					'filename' => $filename
				);

				$image = NewsImageEditor::create($data);
				$path = CMS_DIR . 'images/news/' . $filename;
				if (@move_uploaded_file($file->getLocation(), $path)) {
					@unlink($file->getLocation());

					return array(
						'imageID' => $image->imageID,
						'title' => $image->title,
						'filename' => $image->filename,
						'url' => $image->getURL()
					);
				} else {
					//failure
					$editor = new NewsImageEditor($image);
					$editor->delete();
					throw new UserInputException('image', 'uploadFailed');
				}

			}

		}
		catch (UserInputException $e) {
			$file->setValidationErrorType($e->getType());
		}

		return array(
			'errorType' => $file->getValidationErrorType()
		);
	}
}
