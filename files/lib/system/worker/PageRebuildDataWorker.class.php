<?php
namespace cms\system\worker;

use cms\data\content\ContentEditor;
use cms\data\page\PageAction;
use wcf\data\language\item\LanguageItem;
use wcf\data\language\item\LanguageItemEditor;
use wcf\data\language\Language;
use wcf\system\language\LanguageFactory;
use wcf\system\search\SearchIndexManager;
use wcf\system\WCF;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * Worker implementation to build the search index for pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRebuildDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 100;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = 'cms\data\page\PageList';

	/**
	 * @var HtmlInputProcessor
	 */
	protected $htmlInputProcessor;

	/**
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		$this->objectList->getConditionBuilder()->add('page.pageID BETWEEN ? AND ?', [$this->limit * $this->loopCount + 1, $this->limit * $this->loopCount + $this->limit]);

		parent::execute();

		// reset search index on first cycle
		if (!$this->loopCount) {
			SearchIndexManager::getInstance()->reset('de.codequake.cms.page');
		}

		if (!count($this->objectList)) {
			return;
		}

		WCF::getDB()->beginTransaction();
		/** @var \cms\data\page\Page $page */
		foreach ($this->objectList as $page) {
			/** @var \cms\data\content\Content $content */
			foreach ($page->getContents() as $content) {
				// editor
				$editor = new ContentEditor($content);

				if ($content->getTypeName() == 'de.codequake.cms.content.type.text') {
					$contentData = @unserialize($content->contentData);
					if (!is_array($contentData)) {
						continue;
					}

					$text = $contentData['text'];
					if ($text == WCF::getLanguage()->get($text)) {
						// no lang item
						$this->getHtmlInputProcessor()->process($text,
							'de.codequake.cms.content.type.text', $content->contentID,
							true);
						$contentData['text'] = $this->getHtmlInputProcessor()->getHtml();
					}
					else {
						// is lang item
						$sql = "SELECT  *
								FROM    wcf" . WCF_N . "language_item
								WHERE  languageItem = ?";
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute([$text]);

						while ($row = $statement->fetchArray()) {
							$this->getHtmlInputProcessor()->process($row['languageItemValue'],
								'de.codequake.cms.content.type.text',
								$content->contentID, true);
							$text = $this->getHtmlInputProcessor()->getHtml();

							$itemEditor = new LanguageItemEditor(new LanguageItem($row['languageItemID']));
							$itemEditor->update([
								'languageItemValue' => $text
							]);
						}
					}
					unset($contentData['compiled']);
					$data['contentData'] = serialize($contentData);
				}

				if (!empty($data)) {
					// update data
					$editor->update($data);
				}
			}
		}
		WCF::getDB()->commitTransaction();

		// re-create search index
		$pageAction = new PageAction($this->objectList->getObjects(), 'refreshSearchIndex', ['isBulkProcessing' => true]);
		$pageAction->executeAction();
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
