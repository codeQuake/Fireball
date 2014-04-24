<?php
namespace cms\acp\form;

use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the news category edit form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryEditForm extends AbstractCategoryEditForm {
	public $activeMenuItem = 'cms.acp.menu.link.cms.news.category.add';
	public $objectTypeName = 'de.codequake.cms.category.news';
	public $pageTitle = 'wcf.category.edit';
}
