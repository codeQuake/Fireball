<?php
namespace cms\system\page;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

class PagePermissionHandler extends SingletonFactory{
    protected $permissions = array();
    
    protected function init() {
		$this->permissions = PagePermissionCache::getInstance()->getPermissions(WCF::getUser());
	}
    
    public function getPermission($pageID, $permission) {
		if (isset($this->permissions[$pageID][$permission])) return $this->permissions[$pageID][$permission];
		
		return WCF::getSession()->getPermission('user.cms.page.'.$permission);
	}
}