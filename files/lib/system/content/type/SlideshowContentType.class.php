<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class SlideshowContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-play-sign';

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
