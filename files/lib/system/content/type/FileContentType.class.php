<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\File;
use cms\data\file\FileCache;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-file';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$file = FileCache::getInstance()->getFile($content->fileID);

		WCF::getTPL()->assign(array(
			'file' => $file
		));

		return parent::getOutput($content);
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
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		$contentData = RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData;
		if (isset($contentData['fileID'])) {
			$file = new File($contentData['fileID']);
			if ($file->fileID) {
				WCF::getTPL()->assign(array(
					'file' => $file
				));
			}
		}

		return parent::getFormTemplate();
	}
}
