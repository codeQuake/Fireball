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
	 * @inheritDoc
	 */
	protected $icon = 'fa-picture-o';

	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['imageID'];

	/**
	 * @inheritDoc
	 */
	public $multilingualFields = ['text'];

	/**
	 * @inheritDoc
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
	 * @inheritDoc
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
