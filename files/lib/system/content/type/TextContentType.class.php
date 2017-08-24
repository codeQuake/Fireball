<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\message\censorship\Censorship;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TextContentType extends AbstractSearchableContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-file-text-o';

	/**
	 * @inheritDoc
	 */
	public $multilingualFields = ['text'];
	
	/**
	 * @inheritDoc
	 */
	protected $searchableFields = ['text'];

	/**
	 * @var HtmlInputProcessor
	 */
	protected $htmlInputProcessor = null;

	/**
	 * embedded objects have been loaded already
	 * @var boolean
	 */
	protected $embeddedObjectsLoaded = false;

	/**
	 * @inheritDoc
	 */
	public function getFormTemplate() {
		// init bbcodes
		BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.disallowedBBCodes')));

		return parent::getFormTemplate();
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		$this->loadEmbeddedObjects($content);

		$processor = new HtmlOutputProcessor();
		$processor->process(WCF::getLanguage()->get($content->text), 'de.codequake.cms.content.type.text', $content->contentID);

		return $processor->getHtml();
	}

	/**
	 * Loads the embedded objects.
	 * @param Content $content
	 */
	protected function loadEmbeddedObjects(Content $content) {
		if ($content->hasEmbeddedObjects && !$this->embeddedObjectsLoaded) {
			MessageEmbeddedObjectManager::getInstance()->loadObjects('de.codequake.cms.content.type.text', [$content->contentID]);
			$this->embeddedObjectsLoaded = true;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
		parent::validate($data);

		$this->validateText($data);
	}
	
	/**
	 * Validates the given text (plain and i18n)
	 *
	 * @param $data
	 * @throws \wcf\system\exception\UserInputException
	 */
	protected function validateText(&$data) {
		if (empty($data['text']) && empty($data['i18nValues']['text'])) {
			throw new UserInputException('text');
		}
		
		BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.disallowedBBCodes')));
		
		$this->htmlInputProcessor = new HtmlInputProcessor();
		
		if (empty($data['i18nValues']['text'])) {
			$this->htmlInputProcessor->process($data['text'], 'de.codequake.cms.content.type.text', 0);
			
			// check text length
			if ($this->htmlInputProcessor->appearsToBeEmpty()) {
				throw new UserInputException('text');
			}
			
			$message = $this->htmlInputProcessor->getTextContent();
			$disallowedBBCodes = $this->htmlInputProcessor->validate();
			if (!empty($disallowedBBCodes)) {
				WCF::getTPL()->assign('disallowedBBCodes', $disallowedBBCodes);
				throw new UserInputException('text', 'disallowedBBCodes');
			}
			
			// search for censored words
			if (ENABLE_CENSORSHIP) {
				$result = Censorship::getInstance()->test($message);
				if ($result) {
					WCF::getTPL()->assign('censoredWords', $result);
					throw new UserInputException('text', 'censoredWordsFound');
				}
			}
			
			$data['text'] = $this->htmlInputProcessor->getHtml();
		} else {
			foreach ($data['i18nValues']['text'] as $text) {
				$this->htmlInputProcessor->process($text, 'de.codequake.cms.content.type.text', 0);
				
				// check text length
				if ($this->htmlInputProcessor->appearsToBeEmpty()) {
					throw new UserInputException('text');
				}
				
				$message = $this->htmlInputProcessor->getTextContent();
				$disallowedBBCodes = $this->htmlInputProcessor->validate();
				if (!empty($disallowedBBCodes)) {
					WCF::getTPL()->assign('disallowedBBCodes', $disallowedBBCodes);
					throw new UserInputException('text', 'disallowedBBCodes');
				}
				
				// search for censored words
				if (ENABLE_CENSORSHIP) {
					$result = Censorship::getInstance()->test($message);
					if ($result) {
						WCF::getTPL()->assign('censoredWords', $result);
						throw new UserInputException('text', 'censoredWordsFound');
					}
				}
			}
		}
	}
}
