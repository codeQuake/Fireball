<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\File;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class ImageContentType extends AbstractContentType {

	protected $icon = 'icon-picture';

	public $objectType = 'de.codequake.cms.content.type.image';

	public $isMultilingual = true;

	public $multilingualFields = array(
		'text'
	);

	public function getFormTemplate() {
		WCF::getTPL()->assign('file', new File(0));
		return 'imageContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		WCF::getTPL()->assign(array(
			'data' => $data
		));
		return WCF::getTPL()->fetch('imageContentTypeOutput', 'cms');
	}
}
