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

	protected $className = 'cms\data\stylesheet\StylesheetEditor';

	protected $permissionsDelete = array(
		'admin.cms.style.canAddStylesheet'
	);

	protected $requireACP = array(
		'delete'
	);

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
