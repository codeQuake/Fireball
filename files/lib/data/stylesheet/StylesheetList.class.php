<?php
namespace cms\data\stylesheet;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of stylesheets.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property Stylesheet[] $objects
 * @method Stylesheet[] getObjects()
 */
class StylesheetList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Stylesheet::class;
}
