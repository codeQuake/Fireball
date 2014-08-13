<?php
namespace cms\data\stylesheet;

use cms\data\page\PageList;
use cms\system\layout\LayoutHandler;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes stylesheet-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\stylesheet\StylesheetEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.style.canAddStylesheet');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete', 'update');

	public function delete() {
		parent::delete();

		// kill all layout files to recreate them
		$pageList = new PageList();
		$pageList->readObjects();
		foreach ($pageList->getObjects() as $page) {
			LayoutHandler::getInstance()->deleteStylesheet($page->pageID);
		}
	}

	public function update() {
		parent::update();

		// kill all layout files to recreate them
		$pageList = new PageList();
		$pageList->readObjects();
		foreach ($pageList->getObjects() as $page) {
			LayoutHandler::getInstance()->deleteStylesheet($page->pageID);
		}
	}
}
