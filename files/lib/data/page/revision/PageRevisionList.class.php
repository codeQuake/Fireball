<?php
namespace cms\data\page\revision;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of page revisions.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRevisionList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = PageRevision::class;
}
