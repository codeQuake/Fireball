<?php
namespace cms\system\content;

use cms\data\content\Content;
use cms\system\cache\builder\ContentPermissionCacheBuilder;
use wcf\data\user\User;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages content permissions.
 *
 * @author           Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license          GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package          de.codequake.cms
 */
class ContentPermissionHandler extends SingletonFactory {
	/**
	 * cached permissions
	 * @var        array
	 */
	protected $permissions = [];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->permissions = ContentPermissionCacheBuilder::getInstance()->getData();
	}

	public function resetCache() {
		ContentPermissionCacheBuilder::getInstance()->reset();
	}

	/**
	 * Returns the acl options for the given page and for the given user.
	 * If no user is given, the active user is used.
	 *
	 * @param  Content             $content
	 * @param  \wcf\data\user\User $user
	 * @return array
	 */
	public function getPermissions(Content $content, User $user = null) {
		if ($user === null) {
			$user = WCF::getUser();
		}

		$permissions = [];
		if (isset($this->permissions[$content->contentID])) {
			if (isset($this->permissions[$content->contentID]['group'])) {
				foreach ($user->getGroupIDs() as $groupID) {
					if (isset($this->permissions[$content->contentID]['group'][$groupID])) {
						foreach ($this->permissions[$content->contentID]['group'][$groupID] as $optionName => $optionValue) {
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

			if (isset($this->permissions[$content->contentID]['user']) && isset($this->permissions[$content->contentID]['user'][$user->userID])) {
				foreach ($this->permissions[$content->contentID]['user'][$user->userID] as $optionName => $optionValue) {
					$permissions[$optionName] = $optionValue;
				}
			}
		}

		return $permissions;
	}
}
