<?php
namespace cms\data\file;

use cms\data\CMSDatabaseObject;
use wcf\data\ILinkableObject;
use wcf\system\category\CategoryHandler;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a file.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class File extends CMSDatabaseObject implements ILinkableObject, IRouteController {
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
			return '<span class="icon icon' . $width . ' icon-picture"></span>';
		}
		if (preg_match('/audio/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' icon-music"></span>';
		}
		if (preg_match('/video/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' icon-film"></span>';
		}
		if (preg_match('/pdf/i', $this->fileType)) {
			return '<span class="icon icon' . $width . ' icon-file-text"></span>';
		}
		if (preg_match('/html/i', $this->fileType) || preg_match('/java/i', $this->fileType) || preg_match('/x-c/i', $this->fileType) || preg_match('/css/i', $this->fileType) || preg_match('/javascript/i', $this->fileType)) {
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
