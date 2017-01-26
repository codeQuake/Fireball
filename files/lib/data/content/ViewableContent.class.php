<?php
namespace cms\data\content;

use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a viewable content item.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableContent extends DatabaseObjectDecorator {

	protected static $baseClass = Content::class;
}
