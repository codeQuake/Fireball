<?php
namespace cms\data\page\revision;

use cms\system\cache\builder\PageRevisionCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Functions to edit a page revision.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRevisionEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = 'cms\data\page\revision\PageRevision';

	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		PageRevisionCacheBuilder::getInstance()->reset();
	}
}
