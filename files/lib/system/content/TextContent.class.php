<?php
namespace cms\system\content;
use wcf\data\package\PackageCache;
use wcf\system\bbcode\MessageParser;
use wcf\system\language\I18nHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Text content implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TextContent extends AbstractSearchableContent {
	/**
	 * formatted message
	 * @var	string
	 */
	public $formattedMessage = '';

	/**
	 * @see	\cms\system\content\IContent::readData()
	 */
	public function readData() {
		parent::readData();

		MessageParser::getInstance()->setOutputType('text/html');
		// todo: introduce options for 'enableSmileys', 'enableHtml'
		// and 'enableBBCodes'
		$this->formattedMessage = MessageParser::getInstance()->parse(WCF::getLanguage()->get($this->text), 1, 0, 1);
	}

	/**
	 * @see	\cms\system\content\IContent::getOutput()
	 */
	public function getOutput() {
		WCF::getTPL()->assign(array(
			'formattedMessage' => $this->formattedMessage
		));

		return parent::getOutput();
	}

	/**
	 * @see	\cms\system\content\IContent::setI18nOptions()
	 */
	public function setI18nOptions() {
		parent::setI18nOptions();

		I18nHandler::getInstance()->setOptions('content_'.$this->contentID.'_title', PackageCache::getInstance()->getPackageID('de.codequake.cms'), $this->title, 'cms.content.title\d+');
		I18nHandler::getInstance()->setOptions('content_'.$this->contentID.'_text', PackageCache::getInstance()->getPackageID('de.codequake.cms'), $this->text, 'cms.content.text\d+');
	}

	/**
	 * @see	\cms\system\content\ISearchableContent::getSearchIndexData()
	 */
	public function getSearchIndexData() {
		foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
			$text = $language->get($this->text);
			$this->searchIndexData[$language->languageID][] = $text;
		}

		return parent::getSearchIndexData();
	}
}
