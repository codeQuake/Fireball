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
class FileContentType extends AbstractContentType {

	protected $icon = 'icon-file';

	public $objectType = 'de.codequake.cms.content.type.file';

	public function getFormTemplate() {
		return 'fileContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$file = new File($data['fileID']);
		WCF::getTPL()->assign(array(
			'data' => $data,
			'file' => $image
		));

		return WCF::getTPL()->fetch('fileContentTypeOutput', 'cms');
	}
}
