<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\cache\builder\ContentPermissionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Functions to edit a content item.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = Content::class;

	/**
	 * @inheritDoc
	 */
	public static function resetCache() {
		ContentCacheBuilder::getInstance()->reset();
		ContentPermissionCacheBuilder::getInstance()->reset();
	}
}
