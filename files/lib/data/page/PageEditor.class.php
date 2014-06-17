<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;
use wcf\data\IEditableCachedObject;
use wcf\data\VersionableDatabaseObjectEditor;
use wcf\system\cache\builder\VersionCacheBuilder;
use wcf\system\WCF;

/**
 * Functions to edit a page.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageEditor extends VersionableDatabaseObjectEditor implements IEditableCachedObject {
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
		VersionCacheBuilder::getInstance()->reset();
	}
}
