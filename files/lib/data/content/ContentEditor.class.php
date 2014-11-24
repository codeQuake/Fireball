<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\ContentRevisionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\WCF;

/**
 * Functions to edit a content item.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'cms\data\content\Content';

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
		$sql = "INSERT INTO	cms".WCF_N."_content_revision
					(".$keys.")
			VALUES		(".$values.")";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($statementParameters);

		$id = WCF::getDB()->getInsertID("cms" . WCF_N . "_content_revision", "revisionID");

		return new static::$baseClass($id);
	}

	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		ContentCacheBuilder::getInstance()->reset();
		ContentRevisionCacheBuilder::getInstance()->reset();
	}
}
