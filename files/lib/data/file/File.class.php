<?php
namespace cms\data\file;

use cms\data\CMSDatabaseObject;
use wcf\data\ICategorizedObject;
use wcf\data\ILinkableObject;
use wcf\system\category\CategoryHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a file.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class File extends CMSDatabaseObject implements ICategorizedObject, ILinkableObject, IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'file';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileID';

	/**
	 * @see	\wcf\data\ICategorizedObject::getCategory()
	 */
	public function getCategory() {
		if ($this->categoryID) {
			return CategoryHandler::getInstance()->getCategory($this->categoryID);
		}

		return null;
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
		if (preg_match('/image/i', $this->type)) {
			return '<span class="icon icon' . $width . ' icon-picture"></span>';
		}
		if (preg_match('/audio/i', $this->type)) {
			return '<span class="icon icon' . $width . ' icon-music"></span>';
		}
		if (preg_match('/video/i', $this->type)) {
			return '<span class="icon icon' . $width . ' icon-film"></span>';
		}
		if (preg_match('/pdf/i', $this->type)) {
			return '<span class="icon icon' . $width . ' icon-file-text"></span>';
		}
		if (preg_match('/html/i', $this->type) || preg_match('/java/i', $this->type) || preg_match('/x-c/i', $this->type) || preg_match('/css/i', $this->type) || preg_match('/javascript/i', $this->type)) {
			return '<span class="icon icon' . $width . ' icon-code"></span>';
		}

		return '<span class="icon icon' . $width . ' icon-file"></span>';
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
	 * Returns the physical location of this file.
	 * 
	 * @return	string
	 */
	public function getLocation() {
		return CMS_DIR . 'files/' . substr($this->fileHash, 0, 2) . '/' . $this->getFilename();
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	/**
	 * @todo	Remove method
	 */
	public function getByID($id) {
		return new File($id);
	}

	public function getImageSize() {
		return getimagesize($this->getLocation());
	}
}
