<?php
namespace cms\data\page\revision;

use cms\data\content\ContentAction;
use cms\data\content\ContentList;
use cms\data\page\PageAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes page revision-related actions.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRevisionAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\page\revision\PageRevisionEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.page.canAddPage');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete', 'restore');

	/**
	 * Validates permissions to restore a specific revision.
	 */
	public function validateRestore() {
		WCF::getSession()->checkPermissions(array('admin.cms.page.canAddPage'));

		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Restores a specific revision.
	 */
	public function restore() {
		$revision = $this->getSingleObject();

		WCF::getDB()->beginTransaction();

		// restore page
		$pageAction = new PageAction(array($revision->pageID), 'update', array('data' => @unserialize($revision->data)));
		$pageAction->executeAction();

		// restore contents
		$contentData = @unserialize($revision->contentData);

		$contentList = new ContentList();
		$contentList->getConditionBuilder()->add('content.pageID = ?', array($revision->pageID));
		$contentList->readObjects();

		$existingContentIDs = $contentList->getObjectIDs();
		$oldContents = array();
		foreach ($contentData as $data) {
			$oldContents[$data['contentID']] = $data;
		}

		// delete contents that where created after the revision
		$orphanedElementIDs = array_diff($existingContentIDs, array_keys($oldContents));
		if (!empty($orphanedElementIDs)) {
			$contentAction = new ContentAction($orphanedElementIDs, 'delete');
			$contentAction->executeAction();
		}

		foreach ($oldContents as $oldContent) {
			if (in_array($oldContent['contentID'], $existingContentIDs)) {
				// content this exists => update
				$contentAction = new ContentAction(array($oldContent['contentID']), 'update', array('data' => $oldContent));
				$contentAction->executeAction();
			} else {
				// content was deleted => re-create
				$contentAction = new ContentAction(array($oldContent['contentID']), 'create', array('data' => $oldContent));
				$contentAction->executeAction();
			}
		}

		WCF::getDB()->commitTransaction();
	}
}
