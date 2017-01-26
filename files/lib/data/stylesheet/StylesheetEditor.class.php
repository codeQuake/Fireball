<?php
namespace cms\data\stylesheet;

use cms\system\cache\builder\StylesheetCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Functions to edit a stylesheet.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectEditor::$baseClass
	 */
	protected static $baseClass = Stylesheet::class;

	/**
	 * Delete the compiled stylesheet files. The files will get recompiled
	 * when user visit one of the associated pages.
	 * 
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		$stylesheets = glob(CMS_DIR.'style/style-*.css');
		if ($stylesheets !== false) {
			foreach ($stylesheets as $stylesheet) {
				@unlink($stylesheet);
			}
		}

		StylesheetCacheBuilder::getInstance()->reset();
	}
}
