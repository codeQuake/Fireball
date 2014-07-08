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
class LinkContentType extends AbstractContentType {

	protected $icon = 'icon-link';

	public $objectType = 'de.codequake.cms.content.type.link';

	public $isMultilingual = true;

	public $multilingualFields = array(
		'text',
		'link'
	);

	public function getFormTemplate() {
		return 'linkContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		WCF::getTPL()->assign(array(
			'data' => $data
		));
		return WCF::getTPL()->fetch('linkContentTypeOutput', 'cms');
	}
}
