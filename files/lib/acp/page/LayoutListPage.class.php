<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

/**
 * Shows a list of layouts.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LayoutListPage extends SortablePage {
	public $objectListClassName = 'cms\data\layout\LayoutList';
	public $activeMenuItem = 'cms.acp.menu.link.cms.layout.list';
	public $neededPermissions = array(
		'admin.cms.style.canListLayout'
	);
	public $templateName = 'layoutList';
	public $defaultSortfield = 'layoutID';
	public $validSortFields = array(
		'layoutID',
		'title'
	);
}
