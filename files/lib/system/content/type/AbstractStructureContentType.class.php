<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * Abstract structure content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractStructureContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-columns';

	/**
	 * Returns the css classes for structuring the output
	 * 
	 * @return	string
	 */
	public function getCSSClasses() {
		return '';
	}

	/**
	 * Returns the css classes for child elements
	 * 
	 * @return	string
	 */
	public function getChildCSSClasses(Content $content) {
		return '';
	}
}
