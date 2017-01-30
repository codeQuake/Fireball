<?php
namespace cms\system\file;

use cms\data\file\File;
use cms\system\cache\builder\FilePermissionCacheBuilder;
use wcf\data\user\User;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages file permissions.
 *
 * @author           Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license          GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package          de.codequake.cms
 */
class FilePermissionHandler extends SingletonFactory {
	/**
	 * cached permissions
	 * @var        array
	 */
	protected $permissions = [];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->permissions = FilePermissionCacheBuilder::getInstance()->getData();
	}

	public function resetCache() {
		FilePermissionCacheBuilder::getInstance()->reset();
	}

	/**
	 * Returns the acl options for the given page and for the given user.
	 * If no user is given, the active user is used.
	 *
	 * @param  File                $file
	 * @param  \wcf\data\user\User $user
	 * @return array
	 */
	public function getPermissions(File $file, User $user = null) {
		if ($user === null) {
			$user = WCF::getUser();
		}

		$permissions = [];
		if (isset($this->permissions[$file->fileID])) {
			if (isset($this->permissions[$file->fileID]['group'])) {
				foreach ($user->getGroupIDs() as $groupID) {
					if (isset($this->permissions[$file->fileID]['group'][$groupID])) {
						foreach ($this->permissions[$file->fileID]['group'][$groupID] as $optionName => $optionValue) {
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

			if (isset($this->permissions[$file->fileID]['user']) && isset($this->permissions[$file->fileID]['user'][$user->userID])) {
				foreach ($this->permissions[$file->fileID]['user'][$user->userID] as $optionName => $optionValue) {
					$permissions[$optionName] = $optionValue;
				}
			}
		}

		return $permissions;
	}
}
