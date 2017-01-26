<?php
namespace cms\acp\page;

use cms\data\stylesheet\StylesheetList;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\WCF;

/**
 * Shows a list of stylesheets.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.stylesheet.list';

	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'title';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.fireball.style.canListStylesheet'];

	/**
	 * @inheritDoc
	 */
	public $objectListClassName = StylesheetList::class;

	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['stylesheetID', 'title'];

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.stylesheet'))
		]);
	}
}
