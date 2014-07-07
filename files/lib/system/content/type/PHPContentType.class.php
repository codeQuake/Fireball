<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class PHPContentType extends AbstractContentType {

	protected $icon = 'icon-code';

	public $objectType = 'de.codequake.cms.content.type.php';

	public function getFormTemplate() {
		return 'phpContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$php = substr($data['text'], 5);
		return eval($php);
	}
}
