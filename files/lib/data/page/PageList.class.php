<?php
namespace cms\data\page;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageList extends DatabaseObjectList {
	public $className = 'cms\data\page\Page';
}
