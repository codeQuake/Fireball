<?php
namespace cms\system\content\type;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
class GroupContentType extends AbstractStructureContentType{
	public $objectType = 'de.codequake.cms.content.type.group';

	public function getFormTemplate() {
		return 'groupContentType';
	}

	public function getCSSClasses() {
		return 'contentCollection';
	}
}
