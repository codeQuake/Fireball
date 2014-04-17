<?php
namespace cms\acp\page;
use wcf\page\SortablePage;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class ModuleListPage extends SortablePage{
    public $objectListClassName = 'cms\data\module\ModuleList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.module.list';
    public $neededPermissions = array('admin.cms.content.canManageModule');
    public $templateName = 'moduleList';
    public $defaultSortfield = 'moduleID';
    public $validSortFields = array('moduleID', 'moduleTitle');
}
