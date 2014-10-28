<?php
namespace cms\system\content\type;
use cms\data\file\File;
use wcf\system\WCF;

/**
 * Image content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ImageContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-picture';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormOutput()
	 */
	public function getFormOutput() {
		// todo: really needed?
		WCF::getTPL()->assign('file', new File(0));

		return parent::getFormOutput();
	}
}
