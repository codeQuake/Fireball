<?php
namespace cms\system\content\type;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class GroupContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getCSSClasses()
	 */
	public function getCSSClasses() {
		return 'contentCollection';
	}
}
