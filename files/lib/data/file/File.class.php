<?php
namespace cms\data\file;

use cms\data\folder\Folder;
use cms\data\CMSDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a file.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class File extends CMSDatabaseObject implements IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'file';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileID';

	/**
	 * object of the folder this element belongs to
	 * @var	\cms\data\folder\Folder
	 */
	protected $folder = null;

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	public function getPermission($permission = 'canDownloadFile') {
		return WCF::getSession()->getPermission('user.cms.content.' . $permission);
	}

	public function getIconTag($width = 16) {
		if (preg_match('/image/i', $this->type)) return '<span class="icon icon' . $width . ' icon-picture"></span>';
		if (preg_match('/audio/i', $this->type)) return '<span class="icon icon' . $width . ' icon-music"></span>';
		if (preg_match('/video/i', $this->type)) return '<span class="icon icon' . $width . ' icon-film"></span>';
		if (preg_match('/pdf/i', $this->type)) return '<span class="icon icon' . $width . ' icon-file-text"></span>';
		if (preg_match('/html/i', $this->type) || preg_match('/java/i', $this->type) || preg_match('/x-c/i', $this->type) || preg_match('/css/i', $this->type) || preg_match('/javascript/i', $this->type)) return '<span class="icon icon' . $width . ' icon-code"></span>';
		return '<span class="icon icon' . $width . ' icon-file"></span>';
	}

	public function getFolder() {
		if ($this->folder === null) {
			$this->folder = new Folder($this->folderID);
		}

		return $this->folder;
	}

	public function getURL() {
		if ($this->getFolder() && $this->getFolder()->folderPath != '') return WCF::getPath('cms') . 'files/' . $this->getFolder()->folderPath . '/' . $this->filename;
		return WCF::getPath('cms') . 'files/' . $this->filename;
	}

	public function getRelativeURL() {
		if ($this->getFolder() && $this->getFolder()->folderPath != '') return RELATIVE_CMS_DIR . 'files/' . $this->getFolder()->folderPath . '/' . $this->filename;
		return RELATIVE_CMS_DIR . 'files/' . $this->filename;
	}

	public function getByID($id) {
		return new File($id);
	}

	public function getImageSize() {
		return getimagesize($this->getRelativeURL());
	}
}
