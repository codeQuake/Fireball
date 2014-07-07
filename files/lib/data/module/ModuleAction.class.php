<?php
namespace cms\data\module;

use wcf\data\template\Template;
use wcf\data\template\TemplateAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Executes module-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ModuleAction extends AbstractDatabaseObjectAction {

	protected $className = 'cms\data\module\ModuleEditor';

	protected $permissionsDelete = array(
		'admin.cms.content.canManageModule'
	);

	protected $requireACP = array(
		'delete'
	);
}
