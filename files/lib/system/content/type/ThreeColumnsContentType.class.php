<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class ThreeColumnsContentType extends AbstractStructureContentType {

	protected $icon = 'icon-columns';

	public $objectType = 'de.codequake.cms.content.type.threecolumns';

	public function getFormTemplate() {
		return 'threeColumnContentType';
	}

	public function getCSSClasses() {
		return 'gridContainer';
	}

	public function getChildCSSClasses(Content $content) {
		$parent = $content->getParentContent();
		$data = $parent->handleContentData();
		if (isset($data['width'])) $width = array(
			substr($data['width'], 0, 2),
			substr($data['width'], 2, 3),
			substr($data['width'], 4, 5)
		);
		else $width = array(
			33,
			33,
			33
		);

		switch ($content->showOrder % 3) {
			case 0:
				$width = $width[0];
				break;
			case 1:
				$width = $width[1];
				break;
			case 2:
				$width = $width[2];
				break;
		}
		return 'grid grid' . $width;
	}
}
