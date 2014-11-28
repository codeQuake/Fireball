<?php
namespace cms\data\file;

use cms\data\CMSDatabaseObject;
use wcf\data\ICategorizedObject;
use wcf\system\category\CategoryHandler;
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
class File extends CMSDatabaseObject implements ICategorizedObject, IRouteController {
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
	 * @todo	Really needed? This method is only used in the download
	 * 		controller. Since permissions are not related to a
	 * 		specific file, the controller can easily check the
	 * 		permissions directly.
	 */
	public function getPermission($permission = 'canDownloadFile') {
		return WCF::getSession()->getPermission('user.cms.content.' . $permission);
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->title);
	}

	public function getIconTag($width = 16) {
		if (preg_match('/image/i', $this->type)) return '<span class="icon icon' . $width . ' icon-picture"></span>';
		if (preg_match('/audio/i', $this->type)) return '<span class="icon icon' . $width . ' icon-music"></span>';
		if (preg_match('/video/i', $this->type)) return '<span class="icon icon' . $width . ' icon-film"></span>';
		if (preg_match('/pdf/i', $this->type)) return '<span class="icon icon' . $width . ' icon-file-text"></span>';
		if (preg_match('/html/i', $this->type) || preg_match('/java/i', $this->type) || preg_match('/x-c/i', $this->type) || preg_match('/css/i', $this->type) || preg_match('/javascript/i', $this->type)) return '<span class="icon icon' . $width . ' icon-code"></span>';
		return '<span class="icon icon' . $width . ' icon-file"></span>';
	}

	public function getURL() {
		if ($this->getFolder() && $this->getFolder()->folderPath != '') return WCF::getPath('cms') . 'files/' . $this->getFolder()->folderPath . '/' . $this->filename;
		return WCF::getPath('cms') . 'files/' . $this->filename;
	}

	/**
	 * @todo	Remove method, the actual files should be accessed with
	 * 		their absolute path.
	 */
	public function getRelativeURL() {
		if ($this->getFolder() && $this->getFolder()->folderPath != '') return RELATIVE_CMS_DIR . 'files/' . $this->getFolder()->folderPath . '/' . $this->filename;
		return RELATIVE_CMS_DIR . 'files/' . $this->filename;
	}

	/**
	 * @todo	Remove method
	 */
	public function getByID($id) {
		return new File($id);
	}

	public function getImageSize() {
		return getimagesize($this->getRelativeURL());
	}
}
