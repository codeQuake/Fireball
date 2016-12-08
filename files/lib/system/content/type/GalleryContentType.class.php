<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\FileCache;
use cms\data\file\FileList;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class GalleryContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'fa-picture-o';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$imageIDs = $content->imageIDs;
		
		//neccessary for old data (version 2.0.0 Beta 7 or older)
		if (is_string($content->imageIDs)) $imageIDs = explode(',', $content->imageIDs);

		$list = array();
		foreach ($imageIDs as $imageID) {
			$image = FileCache::getInstance()->getFile($imageID);
			$list[$image->fileID] = $image;
		}

		WCF::getTPL()->assign(array(
			'images' => $list
		));

		return parent::getOutput($content);
	}
	
	/**
	 * @see	\cms\system\content\type\IContentType::getPreview()
	 */
	public function getPreview(Content $content) {
		$imageIDs = $content->imageIDs;
		
		//neccessary for old data (version 2.0.0 Beta 7 or older)
		if (is_string($content->imageIDs)) $imageIDs = explode(',', $content->imageIDs);

		$list = array();
		foreach ($imageIDs as $imageID) {
			$image = FileCache::getInstance()->getFile($imageID);
			$list[$image->fileID] = $image->getTitle();
		}
		
		return StringUtil::truncate(implode(', ', $list), 70);
	}

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		$contentData = RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData;
		if (isset($contentData['imageIDs'])) {
			$imageList = new FileList();
			$imageList->getConditionBuilder()->add('fileID in (?)', array($contentData['imageIDs']));
			$imageList->readObjects();

			WCF::getTPL()->assign(array(
				'imageList' => $imageList
			));
		}

		return parent::getFormTemplate();
	}
}
