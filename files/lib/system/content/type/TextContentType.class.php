<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class TextContentType extends AbstractContentType {

	protected $icon = 'icon-file-text-alt';

	public $objectType = 'de.codequake.cms.content.type.text';

	public $isMultilingual = true;

	public $multilingualFields = array(
		'text'
	);

	public function getFormTemplate() {
		//init bbcodes
		BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.allowedBBCodes')));
		
		return 'textContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		if (isset($data['text'])) return MessageParser::getInstance()->parse(WCF::getLanguage()->get($data['text']), 1, 0, 1);
		return '';
	}
}
