<?php
namespace cms\system\worker;

use cms\data\page\PageAction;
use cms\data\page\PageEditor;
use wcf\system\search\SearchIndexManager;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * worker for refreshing search index
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
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
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		parent::execute();

		if (!$this->loopCount) {
			SearchIndexManager::getInstance()->reset('de.codequake.cms.page');
		}

		//refresh time
		foreach ($this->objectList->getObjects() as $page) {
			if ($page->creationTime == 0) {
				$pageEditor = new PageEditor($page);
				$pageEditor->update(array(
					'creationTime' => TIME_NOW,
					'lastEditTime' => TIME_NOW
				));
			}
		}

		// refresh search index
		$pageAction = new PageAction($this->objectList->getObjects(), 'refreshSearchIndex', array('isBulkProcessing' => true));
		$pageAction->executeAction();

	}
}
