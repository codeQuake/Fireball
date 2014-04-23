<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

/**
 * Shows a list of news images.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsImageListPage extends SortablePage {
	public $objectListClassName = 'cms\data\news\image\NewsImageList';
	public $activeMenuItem = 'cms.acp.menu.link.cms.news.image.list';
	public $neededPermissions = array(
		'admin.cms.news.canManageCategory'
	);
	public $templateName = 'imageList';
	public $defaultSortfield = 'title';
}
