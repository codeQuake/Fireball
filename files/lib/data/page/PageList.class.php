<?php
namespace cms\data\page;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property Page[] $objects
 * @method Page[] getObjects()
 */
class PageList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Page::class;
}
