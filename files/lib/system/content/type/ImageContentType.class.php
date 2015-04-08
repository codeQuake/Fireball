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
class ImageContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-picture';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$multilingualFields
	 */
	public $multilingualFields = array('text');

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		WCF::getTPL()->assign('file', new File(0));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$image = FileCache::getInstance()->getFile($content->imageID);

		WCF::getTPL()->assign(array(
			'image' => $image
		));

		return parent::getOutput($content);
	}

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		$contentData = RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData;
		if (isset($contentData['imageID'])) {
			$file = new File($contentData['imageID']);
			if ($file->fileID) {
				WCF::getTPL()->assign(array(
					'file' => $file
				));
			}
		}

		return parent::getFormTemplate();
	}
}
