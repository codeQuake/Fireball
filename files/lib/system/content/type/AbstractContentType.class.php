<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * Abstract content type implementation.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentType implements IContentType {
	/**
	 * name of the icon to display
	 * @var	string
	 */
	protected $icon = 'icon-unchecked';

	public $multilingualFields = array();

	/**
	 * @see cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {}

	/**
	 * @see \cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		return '';
	}

	/**
	 * @see \cms\system\content\type\IContentType::getIcon()
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		return '';
	}
}
