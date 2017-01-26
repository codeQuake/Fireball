<?php
namespace cms\data\stylesheet;
use cms\system\cache\builder\StylesheetCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the stylesheets cache.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetCache extends SingletonFactory {
	/**
	 * cached stylesheets
	 * @var	array<\cms\data\stylesheet\Stylesheet>
	 */
	protected $stylesheets = [];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->stylesheets = StylesheetCacheBuilder::getInstance()->getData();
	}

	/**
	 * Returns the stylesheet with the given id.
	 * 
	 * @param	integer		$stylesheetID
	 * @return	\cms\data\stylesheet\Stylesheet
	 */
	public function getStylesheet($stylesheetID) {
		if (isset($this->stylesheets[$stylesheetID])) {
			return $this->stylesheets[$stylesheetID];
		}

		return null;
	}
}
