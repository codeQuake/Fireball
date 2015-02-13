<?php
namespace cms\acp\form;

use wcf\acp\form\AbstractCategoryAddForm;

/**
 * Shows the file category add form.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.file.category.add';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.file.canAddFile');

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.codequake.cms.file';
}
