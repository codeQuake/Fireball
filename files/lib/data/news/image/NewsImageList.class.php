<?php
namespace cms\data\news\image;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of news images.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsImageList extends DatabaseObjectList {
	public $className = 'cms\data\news\image\NewsImage';
}
