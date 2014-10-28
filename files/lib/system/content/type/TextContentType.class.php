<?php
namespace cms\system\content\type;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\WCF;

/**
 * Text content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TextContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-file-text-alt';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormOutput()
	 */
	public function getFormOutput() {
		// init bbcodes
		BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));

		return parent::getFormOutput();
	}
}
