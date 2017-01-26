<?php
namespace cms\system\category;

use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * Category implementation for files.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCategoryType extends AbstractCategoryType {
	/**
	 * @inheritDoc
	 */
	protected $hasDescription = false;

	/**
	 * @inheritDoc
	 */
	public function canAddCategory() {
		return $this->canEditCategory();
	}

	/**
	 * @inheritDoc
	 */
	public function canDeleteCategory() {
		return $this->canEditCategory();
	}

	/**
	 * @inheritDoc
	 */
	public function canEditCategory() {
		return WCF::getSession()->getPermission('admin.fireball.file.canAddFile');
	}

	/**
	 * @inheritDoc
	 */
	public function getApplication() {
		return 'cms';
	}
}
