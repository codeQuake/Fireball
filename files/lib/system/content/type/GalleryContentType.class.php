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
		
		// necessary for old data (version 2.0.0 Beta 7 or older)
		if (is_string($content->imageIDs)) $imageIDs = explode(',', $content->imageIDs);
		
		// support for ordered images since 3.0.0 Beta 4
		if (!empty($imageIDs['ordered']) && is_array($imageIDs['ordered'])) {
			$imageIDs = $imageIDs['ordered'];
		}

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
		
		// necessary for old data (version 2.0.0 Beta 7 or older)
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
	 * @return FileList|\cms\data\file\File[]
	 */
	public function getImageList($imageIDs = []) {
		if (empty($imageIDs))
			return [];
		
		if (isset($imageIDs['ordered'])) $orderedIDs = $imageIDs = $imageIDs['ordered'];

		$imageList = new FileList();
		$imageList->getConditionBuilder()->add('fileID in (?)', [$imageIDs]);
		$imageList->readObjects();
		$images = $imageList->getObjects();
		
		if (empty($orderedIDs)) return $images;
		
		$orderedImages = [];
		foreach ($orderedIDs as $order => $imageID) {
			$orderedImages[$order] = $images[$imageID];
		}

		return $orderedImages;
	}
}
