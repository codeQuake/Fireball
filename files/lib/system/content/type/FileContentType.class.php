<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\File;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
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
		$list = new FileList();
		$list->getConditionBuilder()->add('file.folderID =  ?', array(
			'0'
		));
		$list->readObjects();
		$rootList = $list->getObjects();

		$list = new FolderList();
		$list->readObjects();
		$folderList = $list->getObjects();
		WCF::getTPL()->assign(array(
			'rootList' => $rootList,
			'folderList' => $folderList
		));
		return 'fileContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$file = new File($data['fileID']);
		WCF::getTPL()->assign(array(
			'data' => $data,
			'file' => $file
		));

		return WCF::getTPL()->fetch('fileContentTypeOutput', 'cms');
	}
}
