<?php
namespace cms\system\cache\builder;

use cms\data\stylesheet\StylesheetList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches stylesheets.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	public function rebuild(array $parameters) {
		$stylesheetList = new StylesheetList();
		$stylesheetList->readObjects();

		return $stylesheetList->getObjects();
	}
}
