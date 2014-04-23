<?php
namespace cms\acp\page;

use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows a list of news categories.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryListPage extends AbstractCategoryListPage {
	public $activeMenuItem = 'cms.acp.menu.link.cms.news.category.list';
	public $objectTypeName = 'de.codequake.cms.category.news';
	public $pageTitle = 'wcf.category.list';
}
