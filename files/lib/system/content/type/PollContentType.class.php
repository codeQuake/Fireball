<?php
namespace cms\system\content\type;

/**
 * Poll content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PollContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-bar-chart';

	/**
	 * @see	\cms\system\content\type\IContentType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		return MODULE_POLL;
	}
}
