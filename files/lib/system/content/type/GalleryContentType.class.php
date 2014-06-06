<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class GalleryContentType extends AbstractContentType {

	protected $icon = 'icon-picture';

	public $objectType = 'de.codequake.cms.content.type.gallery';

	public function getFormTemplate() {
		return;
	}

	public function getOutput(Content $content) {
		return;
	}
}
