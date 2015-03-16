<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ColumnsContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getCSSClasses()
	 */
	public function getCSSClasses() {
		return 'gridContainer';
	}

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getChildCSSClasses()
	 */
	public function getChildCSSClasses(Content $content) {
		$parent = $content->getParentContent();
		die(var_dump($parent));
	}
	
	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		return '';
	}
}
