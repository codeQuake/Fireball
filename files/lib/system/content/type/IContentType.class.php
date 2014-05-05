<?php
namespace cms\system\content\type;

/**
 * Interface for Basic Contenttypes
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
interface IContentType{
	//type's icon
	protected static $icon;

	//object type name
	public $objectType;

	//validates form data
	public function validate();

	//get Output
	public function getOutput();

	//returns type's icon
	public function getIcon();

	//returns template name
	public function getFormTemplate();
}