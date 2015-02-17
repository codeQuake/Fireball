<?php
namespace cms\page;

use cms\data\file\File;
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
	public static $inlineMimeTypes = array(
		'image/gif',
		'image/jpeg',
		'image/png',
		'application/pdf',
		'image/pjpeg'
	);

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('user.cms.content.canDownloadFile');

	/**
	 * @see	\wcf\page\AbstractPage::$useTemplate
	 */
	public $useTemplate = false;

	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->fileID = intval($_REQUEST['id']);
		$this->file = new File($this->fileID);
		if ($this->file === null) throw new IllegalLinkException();
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		VisitCountHandler::getInstance()->count();
		$this->fileReader = new FileReader($this->file->getLocation(), array(
			'filename' => $this->file->getTitle(),
			'mimeType' => $this->file->fileType,
			'filesize' => $this->file->fileSize,
			'showInline' => (in_array($this->file->fileType, self::$inlineMimeTypes)),
			'enableRangeSupport' => false,
			'lastModificationTime' => TIME_NOW,
			'expirationDate' => TIME_NOW + 31536000,
			'maxAge' => 31536000
		));

		// count downloads
		$fileEditor = new FileEditor($this->file);
		$fileEditor->updateCounters(array(
			'downloads' => 1
		));
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
