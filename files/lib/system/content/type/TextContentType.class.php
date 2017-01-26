<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
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
	 */
	protected function loadEmbeddedObjects(Content $content) {
		if ($content->hasEmbeddedObjects && !$this->embeddedObjectsLoaded) {
			MessageEmbeddedObjectManager::getInstance()->loadObjects('de.codequake.cms.content.type.text', [$content->contentID]);
			$this->embeddedObjectsLoaded = true;
		}
	}
}
