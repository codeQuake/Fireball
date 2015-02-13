<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TwoColumnsContentType extends AbstractStructureContentType {
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
		$data = $parent->handleContentData();

		if (isset($data['width'])) {
			$width = array(
				substr($data['width'], 0, 2),
				substr($data['width'], 2, 2)
			);
		} else {
			$width = array(
				50,
				50
			);
		}

		$width = ($content->showOrder % 2 == 1) ? $width[0] : $width[1];
		return 'grid grid' . $width;
	}
}
