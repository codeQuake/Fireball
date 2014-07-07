<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\FileList;
use wcf\system\WCF;

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
		return 'galleryContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$imageIDs = explode(',', $data['imageIDs']);
		$list = new FileList();
		$list->getConditionBuilder()->add('fileID in (?)', array(
			$imageIDs
		));
		$list->readObjects();
		$list->getObjects();
		WCF::getTPL()->assign(array(
			'images' => $list
		));
		return WCF::getTPL()->fetch('galleryContentTypeOutput', 'cms');
	}
}
