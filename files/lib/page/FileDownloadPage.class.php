<?php
namespace cms\page;

use cms\data\file\FileCache;
use cms\data\file\FileEditor;
use cms\system\counter\VisitCountHandler;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\util\FileReader;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileDownloadPage extends AbstractPage {
	/**
	 * file id
	 * @var	integer
	 */
	public $fileID = 0;

	/**
	 * file object
	 * @var	\cms\data\file\File
	 */
	public $file = null;

	/**
	 * file reader
	 * @var	\wcf\util\FileReader
	 */
	public $fileReader = null;

	/**
	 * list of mime types that are displayed inline
	 * @var	array<string>
	 */
	public static $inlineMimeTypes = [
		'image/gif',
		'image/jpeg',
		'image/png',
		'application/pdf',
		'image/pjpeg'
	];

	/**
	 * @see	\wcf\page\AbstractPage::$useTemplate
	 */
	public $useTemplate = false;

	/**
	 * If set true, this page will display a thumbnail instead of the original file
	 * @var boolean
	 */
	public $loadThumbnail = false;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->fileID = intval($_REQUEST['id']);
		$this->file = FileCache::getInstance()->getFile($this->fileID);
		if ($this->file === null) {
			throw new IllegalLinkException();
		}

		$this->file->checkPermissions();

		if (isset($_GET['thumbnail'])) $this->loadThumbnail = intval($_GET['thumbnail']);

		// check if image file is in cache
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $this->file->uploadTime && in_array($this->file->fileType, self::$inlineMimeTypes)) {
			// send 304
			header("HTTP/1.1 304 Not Modified");
			exit;
		}
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		VisitCountHandler::getInstance()->count();

		if ($this->loadThumbnail && $this->file->hasThumbnail()) {
			$this->fileReader = new FileReader($this->file->getThumbnailLocation(), [
				'filename' => $this->file->getTitle(),
				'mimeType' => $this->file->fileTypeThumbnail,
				'filesize' => $this->file->filesizeThumbnail,
				'showInline' => (in_array($this->file->fileTypeThumbnail, self::$inlineMimeTypes)),
				'enableRangeSupport' => false,
				'lastModificationTime' => $this->file->uploadTime,
				'expirationDate' => TIME_NOW + 31536000,
				'maxAge' => 31536000
			]);
		} else {
			$this->fileReader = new FileReader($this->file->getLocation(), [
				'filename' => $this->file->getTitle(),
				'mimeType' => $this->file->fileType,
				'filesize' => $this->file->filesize,
				'showInline' => (in_array($this->file->fileType, self::$inlineMimeTypes)),
				'enableRangeSupport' => false,
				'lastModificationTime' => $this->file->uploadTime,
				'expirationDate' => TIME_NOW + 31536000,
				'maxAge' => 31536000
			]);
		}

		// count downloads
		$fileEditor = new FileEditor($this->file);
		$fileEditor->updateCounters([
			'downloads' => 1
		]);
	}

	/**
	 * @see	\wcf\page\IPage::show()
	 */
	public function show() {
		parent::show();

		$this->fileReader->send();
		exit();
	}
}
