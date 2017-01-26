<?php
namespace cms\system\page;

use cms\data\page\Page;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use wcf\data\user\User;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages page permissions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePermissionHandler extends SingletonFactory {
	/**
	 * cached permissions
	 * @var	array
	 */
	protected $permissions = [];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->permissions = PagePermissionCacheBuilder::getInstance()->getData();
	}

	public function resetCache() {
		PagePermissionCacheBuilder::getInstance()->reset();
	}

	/**
	 * Returns the acl options for the given page and for the given user.
	 * If no user is given, the active user is used.
	 *
	 * @param        \cms\data\page\Page $page
	 * @param        \wcf\data\user\User $user
	 * @return array
	 */
	public function getPermissions(Page $page, User $user = null) {
		if ($user === null) {
			$user = WCF::getUser();
		}

		$permissions = [];
		if (isset($this->permissions[$page->pageID])) {
			if (isset($this->permissions[$page->pageID]['group'])) {
				foreach ($user->getGroupIDs() as $groupID) {
					if (isset($this->permissions[$page->pageID]['group'][$groupID])) {
						foreach ($this->permissions[$page->pageID]['group'][$groupID] as $optionName => $optionValue) {
							if (isset($permissions[$optionName])) {
								$permissions[$optionName] = $permissions[$optionName] || $optionValue;
							}
							else {
								$permissions[$optionName] = $optionValue;
							}
						}
					}
				}
			}

			if (isset($this->permissions[$page->pageID]['user']) && isset($this->permissions[$page->pageID]['user'][$user->userID])) {
				foreach ($this->permissions[$page->pageID]['user'][$user->userID] as $optionName => $optionValue) {
					$permissions[$optionName] = $optionValue;
				}
			}
		}

		return $permissions;
	}
}
