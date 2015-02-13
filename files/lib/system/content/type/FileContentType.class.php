<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\File;
use cms\data\file\FileList;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-file';

	/**
	 * @see	\cms\system\content\type\IContentType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		$fileList = new FileList();
		$count = $fileList->countObjects();

		return $count > 0;
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$file = new File($data['fileID']);
		WCF::getTPL()->assign(array(
			'data' => $data,
			'file' => $file
		));

		return WCF::getTPL()->fetch('fileContentType', 'cms');
	}
}
