<?php
namespace cms\data\file;

use cms\system\file\FilePermissionHandler;
use wcf\data\DatabaseObject;
use wcf\data\ILinkableObject;
use wcf\data\IPermissionObject;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a file.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property-read	integer		$fileID		    id of the file
 * @property-read	string		$title		    filename
 * @property-read	integer		$filesize		filesize in bytes
 * @property-read	string		$fileType		mime type of the file
 * @property-read	string		$fileHash		hash of the file
 * @property-read	integer		$uploadTime		timestamp of upload
 * @property-read	integer		$downloads		amount of downloads
 */
class File extends DatabaseObject implements ILinkableObject, IRouteController, IPermissionObject {
	/**
	 * list of category ids
	 * @var	array<integer>
	 */
	public $categoryIDs = array();

	/**
	 * list of categories
	 * @var	array<\wcf\data\category\Category>
	 */
	protected $categories = null;

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'file';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileID';

	/**
	 * list of mime types that support thumbnail generation
	 * @var	array<string>
	 */
	public static $thumbnailMimeTypes = array(
		'image/gif',
		'image/jpeg',
		'image/png',
		'image/pjpeg'
	);

	/**
	 * Returns the category ids of this file.
	 * 
	 * @return	array<integer>
	 */
	public function getCategoryIDs() {
		if (empty($this->categoryIDs)) {
			$sql = "SELECT	categoryID
				FROM	cms".WCF_N."_file_to_category
				WHERE	fileID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->fileID));

			while ($row = $statement->fetchArray()) {
				$this->categoryIDs[] = $row['categoryID'];
			}
		}

		return $this->categoryIDs;
	}

	/**
	 * Returns the categories of this file.
	 * 
	 * @return	array<\wcf\data\category\Category>
	 */
	public function getCategories() {
		if ($this->categories === null) {
			$this->categories = array();

			foreach ($this->getCategoryIDs() as $categoryID) {
				$this->categories[$categoryID] = CategoryHandler::getInstance()->getCategory($categoryID);
			}
		}

		return $this->categories;
	}

	/**
	 * Returns the actual file name of this file.
	 * 
	 * @return	string
	 */
	public function getFilename() {
		return $this->fileID . '-' . $this->fileHash;
	}

	/**
	 * Returns an icon tag representing the mime type of this file.
	 * 
	 * @return	string
	 */
	public function getIconTag($width = 16) {
		if (preg_match('/image/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' fa-picture"></span>';
		}
		if (preg_match('/audio/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' fa-music"></span>';
		}
		if (preg_match('/video/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' fa-film"></span>';
		}
		if (preg_match('/pdf/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' fa-file-text"></span>';
		}
		if (preg_match('/html/i', $this->fileType) || preg_match('/java/i', $this->fileType) || preg_match('/x-c/i', $this->fileType) || preg_match('/css/i', $this->fileType) || preg_match('/javascript/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' fa-code"></span>';
		}

		return '<span class="icon icon' . $width . ' fa-file"></span>';
	}

	/**
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('FileDownload', array(
			'application' => 'cms',
			'forceFrontend' => true,
			'object' => $this
		));
	}

	/**
	 * Generates a link to the thumbnail of the image
	 * @return string
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getThumbnailLink() {
		// fallback
		if (!$this->hasThumbnail()) {
			return $this->getLink();
		}

		return LinkHandler::getInstance()->getLink('FileDownload', array(
			'application' => 'cms',
			'forceFrontend' => true,
			'id' => $this->fileID,
			'thumbnail' => 1
		));
	}

	/**
	 * Returns the physical location of this file.
	 *
	 * @return	string
	 */
	public function getLocation() {
		return CMS_DIR . 'files/' . substr($this->fileHash, 0, 2) . '/' . $this->getFilename();
	}

	/**
	 * Returns the physical location of the thumbnail of this file.
	 *
	 * @return	string
	 */
	public function getThumbnailLocation() {
		return CMS_DIR . 'files/thumbnails/' . substr($this->fileHash, 0, 2) . '/' . $this->getFilename();
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	public function getImageSize() {
		return getimagesize($this->getLocation());
	}
	
	/**
	 * Indicates whether a file is an image or not. Indication via MIME-Type
	 * @return boolean
	 */
	public function isImage() {
		if (preg_match('/image/i', $this->fileType)) {
			return true;
		}
		return false;
	}

	/**
	 * Indicates whether a file has a thumbnail
	 * @return boolean
	 */
	public function hasThumbnail() {
		return !empty($this->filesizeThumbnail) && $this->isImage();
	}

	/**
	 * @see        \wcf\data\IPermissionObject::checkPermissions()
	 * @param array $permissions
	 * @throws PermissionDeniedException
	 */
	public function checkPermissions(array $permissions = array('user.canDownloadFile')) {
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * @see	\wcf\data\IPermissionObject::getPermission()
	 */
	public function getPermission($permission) {
		$permissions = FilePermissionHandler::getInstance()->getPermissions($this);

		$aclPermission = str_replace(array('user.', 'mod.', 'admin.'), array('', '', ''), $permission);
		if (isset($permissions[$aclPermission])) {
			return $permissions[$aclPermission];
		}

		// why the hell is this permission located under content?!
		if ($permission == 'user.canDownloadFile') {
			return WCF::getSession()->getPermission('user.fireball.content.canDownloadFile');
		}

		$globalPermission = str_replace(array('user.', 'mod.', 'admin.'), array('user.fireball.file.', 'mod.fireball.', 'user.fireball.file.'), $permission);
		return WCF::getSession()->getPermission($globalPermission);
	}
}
