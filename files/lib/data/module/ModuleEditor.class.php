<?php
namespace cms\data\module;

use wcf\data\DatabaseObjectEditor;

/**
 * Functions to edit a module.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ModuleEditor extends DatabaseObjectEditor {

	protected static $baseClass = 'cms\data\module\Module';
}
