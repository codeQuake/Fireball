<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\FileCache;
use cms\data\file\FileList;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class GalleryContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-picture-o';

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		$imageIDs = $content->imageIDs;
		
		//neccessary for old data (version 2.0.0 Beta 7 or older)
		if (is_string($content->imageIDs)) $imageIDs = explode(',', $content->imageIDs);

		$list = [];
		foreach ($imageIDs as $imageID) {
			$image = FileCache::getInstance()->getFile($imageID);
			$list[$image->fileID] = $image;
		}

		WCF::getTPL()->assign([
			'images' => $list
		]);

		return parent::getOutput($content);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPreview(Content $content) {
		$imageIDs = $content->imageIDs;
		
		//neccessary for old data (version 2.0.0 Beta 7 or older)
		if (is_string($content->imageIDs)) $imageIDs = explode(',', $content->imageIDs);

		$list = [];
		foreach ($imageIDs as $imageID) {
			$image = FileCache::getInstance()->getFile($imageID);
			$list[$image->fileID] = $image->getTitle();
		}
		
		return StringUtil::truncate(implode(', ', $list), 70);
	}

	/**
	 * @param array $imageIDs
	 * @return FileList
	 */
	public function getImageList($imageIDs = []) {
		if (empty($imageIDs))
			return [];

		$imageList = new FileList();
		$imageList->getConditionBuilder()->add('fileID in (?)', [$imageIDs]);
		$imageList->readObjects();

		return $imageList;
	}
}
