<?php
namespace cms\system\worker;

use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\system\page\handler\PagePageHandler;
use wcf\data\package\PackageCache;
use wcf\data\page\PageAction as WCFPageAction;
use wcf\system\language\LanguageFactory;
use wcf\system\search\SearchIndexManager;
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
	 * @inheritDoc
	 */
	protected $limit = 100;

	/**
	 * @inheritDoc
	 */
	protected $objectListClassName = PageList::class;

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$this->objectList->getConditionBuilder()->add('page.pageID BETWEEN ? AND ?', [$this->limit * $this->loopCount + 1, $this->limit * $this->loopCount + $this->limit]);
		$packageID = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms')->packageID;

		parent::execute();

		// reset search index on first cycle
		if (!$this->loopCount) {
			SearchIndexManager::getInstance()->reset('de.codequake.cms.page');
		}

		if (!count($this->objectList)) {
			return;
		}

		/** @var \cms\data\page\Page $page */
		foreach ($this->objectList as $page) {
			$pageEditor = new PageEditor($page);
			$parentPage = $page->getParentPage();

			$availableLanguages = LanguageFactory::getInstance()->getLanguages();
			$contents = [];
			foreach ($availableLanguages as $language) {
				$contents[$language->languageID] = [
					'title' => $language->get($page->title),
					'content' => '',
					'metaDescription' => $language->get($page->metaDescription),
					'metaKeywords' => $language->get($page->metaKeywords),
					'customURL' => ''
				];
			}

			if ($page->wcfPageID === null) {
				$wcfPageAction = new WCFPageAction([], 'update', [
					'data' => [
						'identifier' => 'de.codequake.cms.page' . $page->pageID,
						'name' => $page->getTitle(),
						'pageType' => 'system',
						'packageID' => $packageID,
						'applicationPackageID' => $packageID,
						'handler' => PagePageHandler::class,
						'controllerCustomURL' => $page->getAlias(),
						'lastUpdateTime' => $page->getLastEditTime(),
						'parentPageID' => $parentPage === null ? null : $parentPage->wcfPageID
					],
					'content' => $contents
				]);
				$wcfPage = $wcfPageAction->executeAction();
				$pageEditor->update(['wcfPageID' => $wcfPage['returnValues']->pageID]);
			} else {
				$wcfPageAction = new WCFPageAction([$page->wcfPageID], 'update', [
					'data' => [
						'name' => $page->getTitle(),
						'lastUpdateTime' => $page->getLastEditTime(),
						'parentPageID' => $parentPage === null ? null : $parentPage->wcfPageID
					],
					'content' => $contents
				]);
				$wcfPageAction->executeAction();
			}
		}

		// re-create search index
		$pageAction = new PageAction($this->objectList->getObjects(), 'refreshSearchIndex', ['isBulkProcessing' => true]);
		$pageAction->executeAction();
	}
}
