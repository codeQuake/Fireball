<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class HTMLContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-code';

	public function getFormTemplate() {
		return 'htmlContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		return $data['text'];
	}
}
