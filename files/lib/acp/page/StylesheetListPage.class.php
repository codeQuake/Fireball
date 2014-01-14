<?php
namespace cms\acp\page;
use wcf\page\SortablePage;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class StylesheetListPage extends SortablePage{
    public $objectListClassName = 'cms\data\stylesheet\StylesheetList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.stylesheet.list';
    public $neededPermissions = array('admin.cms.style.canListStylesheet');
    public $templateName = 'stylesheetList';
    public $defaultSortfield = 'sheetID';
    public $validSortFields = array('sheetID', 'title');
}