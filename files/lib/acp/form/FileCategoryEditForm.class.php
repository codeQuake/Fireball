<?php
namespace cms\acp\form;

use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the file category edit form.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.file';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.cms.file.canAddFile');

	/**
	 * @see	\wcf\acp\form\AbstractCategoryAddForm::$objectTypeName
	 */
	public $objectTypeName = 'de.codequake.cms.file';
}
