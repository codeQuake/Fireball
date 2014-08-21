<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * Interface for Basic Contenttypes
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface IContentType {
	/**
	 * Validates the submitted form data. In case of invalid inputs, throw
	 * an instance of '\wcf\system\exception\UserInputException'
	 */
	public function validate($data);

	/**
	 * Returns the formatted output for the given content.
	 * 
	 * @param	\cms\data\content\Content	$content
	 * @return	string
	 */
	public function getOutput(Content $content);

	/**
	 * Returns the icon name (with icon prefix) for this content type.
	 * 
	 * @return	string
	 */
	public function getIcon();

	/**
	 * Returns the template name for the acp forms
	 * 
	 * @return	string
	 */
	public function getFormTemplate();
}
