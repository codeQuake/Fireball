<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ThreeColumnsContentType extends AbstractStructureContentType {
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

		if (isset($parent->width)) {
			$width = array(
				substr($parent->width, 0, 2),
				substr($parent->width, 2, 2),
				substr($parent->width, 4, 2)
			);
		} else {
			$width = array(
				33,
				33,
				33
			);
		}
		
		switch ($content->showOrder % 3) {
			case 0:
				$gridWidth = $width[2];
			break;

			case 1:
				$gridWidth = $width[0];
			break;

			case 2:
				$gridWidth = $width[1];
			break;
		}

		return 'grid grid' . $gridWidth;
	}
	
	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		return '';
	}
}
