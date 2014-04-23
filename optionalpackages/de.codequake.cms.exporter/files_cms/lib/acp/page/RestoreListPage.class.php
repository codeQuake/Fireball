<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.exporter
 */
class RestoreListPage extends SortablePage {
	public $objectListClassName = 'cms\data\restore\RestoreList';
	public $activeMenuItem = 'cms.acp.menu.link.cms.restore.list';
	public $neededPermissions = array(
		'admin.cms.restore.canRestore'
	);
	public $templateName = 'restoreList';
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	public $validSortFields = array(
		'time'
	);
}
