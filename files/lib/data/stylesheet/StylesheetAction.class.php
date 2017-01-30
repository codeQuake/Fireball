<?php
namespace cms\data\stylesheet;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes stylesheet-related actions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = StylesheetEditor::class;

	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.fireball.style.canAddStylesheet'];

	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['delete', 'update'];
}
