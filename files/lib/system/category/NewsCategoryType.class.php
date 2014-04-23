<?php
namespace cms\system\category;

use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryType extends AbstractCategoryType {
	protected $langVarPrefix = 'cms.category.news';
	protected $forceDescription = false;
	protected $maximumNestingLevel = 1;
	protected $objectTypes = array(
		'com.woltlab.wcf.acl' => 'de.codequake.cms.category.news'
	);

	public function getApplication() {
		return 'cms';
	}

	public function canAddCategory() {
		return $this->canEditCategory();
	}

	public function canDeleteCategory() {
		return $this->canEditCategory();
	}

	public function canEditCategory() {
		return WCF::getSession()->getPermission('admin.cms.news.canManageCategory');
	}
}
