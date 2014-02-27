<?php
namespace cms\acp\page;
use wcf\page\SortablePage;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PageListPage extends SortablePage{
    
    public $objectListClassName = 'cms\data\page\PageList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';
    public $neededPermissions = array('admin.cms.page.canListPage');
    public $templateName = 'pageList';
    public $defaultSortfield = 'pageID';
    public $validSortFields = array('pageID', 'title');
    
}