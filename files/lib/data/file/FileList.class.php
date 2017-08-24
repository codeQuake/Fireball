<?php
namespace cms\data\file;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of files.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property File[] $objects
 * @method File[] getObjects()
 */
class FileList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = File::class;
}
