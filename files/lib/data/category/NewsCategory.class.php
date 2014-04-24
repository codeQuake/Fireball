<?php
namespace cms\data\category;

use wcf\data\category\AbstractDecoratedCategory;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\category\CategoryHandler;
use wcf\system\category\CategoryPermissionHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a news category.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategory extends AbstractDecoratedCategory implements IBreadcrumbProvider {

	const OBJECT_TYPE_NAME = 'de.codequake.cms.category.news';
	protected $permissions = null;

	public function isAccessible() {
		if ($this->getObjectType()->objectType != self::OBJECT_TYPE_NAME) return false;
		return $this->getPermission('canViewCategory');
	}

	public function getPermission($permission) {
		if ($this->permissions === null) {
			$this->permissions = CategoryPermissionHandler::getInstance()->getPermissions($this->getDecoratedObject());
		}
		if (isset($this->permissions[$permission])) {
			return $this->permissions[$permission];
		}
		return (WCF::getSession()->getPermission('user.cms.news.' . $permission) || WCF::getSession()->getPermission('mod.cms.news.' . $permission) || WCF::getSession()->getPermission('admin.cms.news.' . $permission));
	}

	public function getBreadcrumb() {
		return new Breadcrumb(WCF::getLanguage()->get($this->title), LinkHandler::getInstance()->getLink('NewsCategory', array(
			'application' => 'cms',
			'object' => $this->getDecoratedObject()
		)));
	}

	public static function getAccessibleCategoryIDs($permissions = array('canViewCategory')) {
		$categoryIDs = array();
		foreach (CategoryHandler::getInstance()->getCategories(self::OBJECT_TYPE_NAME) as $category) {
			$result = true;
			$category = new NewsCategory($category);
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			if ($result) {
				$categoryIDs[] = $category->categoryID;
			}
		}
		return $categoryIDs;
	}
}
