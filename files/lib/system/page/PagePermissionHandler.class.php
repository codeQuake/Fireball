<?php
namespace cms\system\page;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use cms\system\cache\builder\PagePermissionCacheBuilder;
use cms\data\page\Page;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PagePermissionHandler extends SingletonFactory{
    protected $permissions = array();
    
    protected function init() {
		$this->permissions = PagePermissionCacheBuilder::getInstance()->getData();
	}
    
    public function resetCache() {
		PagePermissionCacheBuilder::getInstance()->reset();
	}
    
    public function getPermission(Page $page) {
        $user = WCF::getUser();
        $permissions = array();
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