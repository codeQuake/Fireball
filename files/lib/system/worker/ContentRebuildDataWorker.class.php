<?php
namespace cms\system\worker;

use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use wcf\data\language\item\LanguageItem;
use wcf\data\language\item\LanguageItemEditor;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\worker\AbstractRebuildDataWorker;
use wcf\system\WCF;

/**
 * Worker implementation to rebuild the contents.
 *
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentRebuildDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 100;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = ContentList::class;

	/**
	 * @var HtmlInputProcessor
	 */
	protected $htmlInputProcessor;

	/**
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		$this->objectList->getConditionBuilder()->add('content.contentID BETWEEN ? AND ?', [$this->limit * $this->loopCount + 1, $this->limit * $this->loopCount + $this->limit]);

		parent::execute();

		if (!count($this->objectList)) {
			return;
		}

		WCF::getDB()->beginTransaction();
		/** @var \cms\data\content\Content $content */
		foreach ($this->objectList as $content) {
			// editor
			$editor = new ContentEditor($content);
			$data = [];

			if ($content->getTypeName() == 'de.codequake.cms.content.type.text') {
				if (!is_array($content->contentData) || $content->contentData['enableHtml']) {
					continue;
				}

				$contentData = $content->contentData;

				$text = $contentData['text'];
				if ($text == WCF::getLanguage()->get($text)) {
					// no lang item
					$this->getHtmlInputProcessor()->process($text, 'de.codequake.cms.content.type.text', $content->contentID, true);
					$contentData['text'] = $this->getHtmlInputProcessor()->getHtml();
					$text = $contentData['text'];
				}
				else {
					// is lang item
					$sql = "SELECT  *
							FROM    wcf" . WCF_N . "_language_item
							WHERE  languageItem = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute([$text]);

					while ($row = $statement->fetchArray()) {
						$this->getHtmlInputProcessor()->process($row['languageItemValue'], 'de.codequake.cms.content.type.text', $content->contentID, true);
						$text = $this->getHtmlInputProcessor()->getHtml();

						$itemEditor = new LanguageItemEditor(new LanguageItem($row['languageItemID']));
						$itemEditor->update([
							'languageItemValue' => $text
						]);
					}
				}
				unset($contentData['compiled']);

				if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->getHtmlInputProcessor())) {
					$contentData['hasEmbeddedObjects'] = 1;
				} else {
					$contentData['hasEmbeddedObjects'] = 1;
				}

				$contentData['enableHtml'] = 1;

				$data['contentData'] = serialize($contentData);
			}

			if (!empty($data)) {
				// update data
				$editor->update($data);
			}
		}
		WCF::getDB()->commitTransaction();
	}

	/**
	 * @return HtmlInputProcessor
	 */
	protected function getHtmlInputProcessor() {
		if ($this->htmlInputProcessor === null) {
			$this->htmlInputProcessor = new HtmlInputProcessor();
		}

		return $this->htmlInputProcessor;
	}
}
