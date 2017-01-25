<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\package\PackageCache;
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
	protected $icon = 'fa-file-text-o';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$multilingualFields
	 */
	public $multilingualFields = ['text'];
	
	/**
	 * @see	\cms\system\content\type\AbstractSearchableContentType::$searchableFields
	 */
	protected $searchableFields = ['text'];

	/**
	 * @see \cms\system\content\type\AbstractContentType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		$package = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.wysiwyg.acp');
		return ($package !== null);
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		// init bbcodes
		BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.disallowedBBCodes')));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		MessageParser::getInstance()->setOutputType('text/html');

		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($content->text), 1, 1, 1);
	}
}
