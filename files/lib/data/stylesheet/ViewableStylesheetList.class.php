<?php
namespace cms\data\stylesheet;

/**
 * Represents a list of viewable stylesheets.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewableStylesheetList extends StylesheetList {

	public $decoratorClassName = 'cms\data\stylesheet\ViewableStylesheet';
}
