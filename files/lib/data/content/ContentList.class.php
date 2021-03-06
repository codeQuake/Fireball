<?php
namespace cms\data\content;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of content items.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property Content[] $objects
 * @method Content[] getObjects()
 */
class ContentList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Content::class;
}
