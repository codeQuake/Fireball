<?php

namespace cms\system\content\type;
use cms\data\content\Content;
use wcf\system\WCF;

/**
 *
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
class HeadlineContentType extends AbstractContentType {
	protected $icon = 'icon-underline';
	public $objectType = 'de.codequake.cms.content.type.headline';
	public $isMultilingual = true;
	public $multilingualFields = array('text');

	public function getFormTemplate() {
		return 'headlineContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		WCF::getTPL()->assign(array('data' => $data));
		return WCF::getTPL()->fetch('headlineContentTypeOutput', 'cms');
	}
}
