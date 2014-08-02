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
class SlideshowContentType extends AbstractStructureContentType {

	protected $icon = 'icon-play-sign';

	public $objectType = 'de.codequake.cms.content.type.slideshow';

	public function getFormTemplate() {
		return 'slideshowContentType';
	}

	public function getCSSClasses() {
		return 'fireballSlideContainer';
	}

	public function getChildCSSClasses(Content $content) {
		return 'fireballSlide';
	}
}
