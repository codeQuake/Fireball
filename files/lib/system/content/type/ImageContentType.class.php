<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\File;
use cms\data\file\FileCache;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ImageContentType extends FileContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'fa-picture-o';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$previewFields
	 */
	protected $previewFields = ['imageID'];

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$multilingualFields
	 */
	public $multilingualFields = ['text'];

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$image = FileCache::getInstance()->getFile($content->imageID);

		WCF::getTPL()->assign([
			'image' => $image,
			'width' => $content->width
		]);

		return AbstractContentType::getOutput($content);
	}

	/**
	 * @see cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {
		if (!isset($data['imageID'])) {
			throw new UserInputException('imageID');
		}

		$file = new File($data['imageID']);
		if (!$file->fileID) {
			throw new UserInputException('imageID');
		}
	}

	/**
	 * @param $imageID  image id
	 * @return File
	 */
	public function getImage($imageID) {
		return new File($imageID);
	}
}
