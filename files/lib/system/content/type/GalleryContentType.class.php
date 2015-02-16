<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\file\FileList;
use wcf\system\WCF;

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
	protected $icon = 'icon-picture';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$imageIDs = explode(',', $content->imageIDs);

		$list = new FileList();
		$list->getConditionBuilder()->add('fileID in (?)', array($imageIDs));
		$list->readObjects();

		WCF::getTPL()->assign(array(
			'images' => $list
		));

		return parent::getOutput($content);
	}
}
