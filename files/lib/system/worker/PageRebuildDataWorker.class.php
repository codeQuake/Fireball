<?php
namespace cms\system\worker;

use cms\data\page\PageAction;
use cms\data\page\PageList;
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
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 100;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = PageList::class;

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

		// re-create search index
		$pageAction = new PageAction($this->objectList->getObjects(), 'refreshSearchIndex', ['isBulkProcessing' => true]);
		$pageAction->executeAction();
	}
}
