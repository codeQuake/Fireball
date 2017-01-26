<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class SlideshowContentType extends AbstractStructureContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-play';

	/**
	 * @inheritDoc
	 */
	public function getCSSClasses() {
		return 'fireballSlideContainer';
	}

	/**
	 * @inheritDoc
	 */
	public function getChildCSSClasses(Content $content) {
		return 'fireballSlide';
	}

}
