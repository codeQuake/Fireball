<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class HTMLContentType extends AbstractContentType {

	protected $icon = 'icon-code';

	public $objectType = 'de.codequake.cms.content.type.html';

	public function getFormTemplate() {
		return 'htmlContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		return $data['text'];
	}
}
