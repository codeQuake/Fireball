<?php
namespace cms\data\content;

/**
 * Represents a list of viewable content items.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableContentList extends ContentList {

	public $decoratorClassName = ViewableContent::class;
}
