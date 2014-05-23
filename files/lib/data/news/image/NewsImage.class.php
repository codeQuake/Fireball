<?php
namespace cms\data\news\image;

use cms\data\CMSDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a news image.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsImage extends CMSDatabaseObject implements IRouteController {
	protected static $databaseTableName = 'news_image';
	protected static $databaseTableIndexName = 'imageID';

	public function __construct($id, $row = null, $object = null) {
		if ($id !== null) {
			$sql = "SELECT *
                    FROM " . static::getDatabaseTableName() . "
                    WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$id
			));
			$row = $statement->fetchArray();

			if ($row === false) $row = array();
		}

		parent::__construct(null, $row, $object);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getURL() {
		$path = RELATIVE_CMS_DIR . 'images/news/' . $this->filename;
		return $path;
	}

	public function getImageTag($width = 0) {
		$file = $this->getURL();
		return $width != 0 ? '<img src="' . $file . '" alt="' . $this->title . '" style="width: ' . $width . 'px" />' : '<img src="' . $file . '" alt="' . $this->title . '" class="jsResizeImage" />';
	}
}
