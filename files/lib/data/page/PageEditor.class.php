<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;

/**
 * Functions to edit a page.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	protected static $baseClass = 'cms\data\page\Page';

	public function setAsHome() {
		$sql = "UPDATE	cms" . WCF_N . "_page
			SET	isHome = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			0
		));

		$sql = "UPDATE	cms" . WCF_N . "_page
			SET	isHome = ?
			WHERE	pageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			1,
			$this->pageID
		));
	}

	public static function resetCache() {
		PageCacheBuilder::getInstance()->reset();
	}

	public static function createRevision(array $parameters = array()) {
		$keys = $values = '';
		$statementParameters = array();
		foreach ($parameters as $key => $value) {
			if (!empty($keys)) {
				$keys .= ',';
				$values .= ',';
			}

			$keys .= $key;
			$values .= '?';
			$statementParameters[] = $value;
		}

		// save object
		$sql = "INSERT INTO	cms".WCF_N."_page_revision (".$keys.")
				VALUES (".$values.")";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($statementParameters);

		$id = WCF::getDB()->getInsertID("cms".WCF_N."_page_revision", "revisionID");

		return new static::$baseClass($id);
	}
}
