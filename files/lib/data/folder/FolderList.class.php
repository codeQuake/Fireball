<?php
namespace cms\data\folder;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of folders.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FolderList extends DatabaseObjectList {

	public $className = 'cms\data\folder\Folder';
}
