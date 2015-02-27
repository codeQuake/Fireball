<?php
namespace cms\data\page\revision;

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

		$pageAction = new PageAction(array($revision->pageID), 'update', array('data' => @unserialize($revision->data)));
		$pageAction->executeAction();
	}
}
