<?php
namespace cms\data\page\revision;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of page revisions.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property PageRevision[] $objects
 * @method PageRevision[] getObjects()
 */
class PageRevisionList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = PageRevision::class;
}
