<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TextContentType extends AbstractSearchableContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-file-text-alt';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$multilingualFields
	 */
	public $multilingualFields = array('text');
	
	/**
	 * @see	\cms\system\content\type\AbstractSearchableContentType::$searchableFields
	 */
	protected $searchableFields = array('text');

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		// init bbcodes
		BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		MessageParser::getInstance()->setOutputType('text/html');

		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($content->text), 1, 0, 1);
	}
}
