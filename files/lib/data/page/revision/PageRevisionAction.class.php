<?php
namespace cms\data\page\revision;

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
	protected $requireACP = array('delete');
}
