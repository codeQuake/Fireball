<?php
namespace cms\data\page;
use cms\data\content\PageContentList;
use wcf\system\request\IRouteController;
use cms\data\CMSDatabaseObject;
use cms\system\layout\LayoutHandler;
use cms\system\page\PagePermissionHandler;
use wcf\system\WCF;

class Page extends CMSDatabaseObject implements IRouteController{

    protected static $databaseTableName = 'page';
    protected static $databaseTableIndexName = 'pageID';
    public $contentList = null;
    
    
    public function __construct($id, $row = null, $object = null){
        if ($id !== null) {
             $sql = "SELECT *
                    FROM ".static::getDatabaseTableName()."
                    WHERE (".static::getDatabaseTableIndexName()." = ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array($id));
            $row = $statement->fetchArray();

            if ($row === false) $row = array();
         }

        parent::__construct(null, $row, $object);
    }
    
    public function getTitle(){
        return $this->title;
    }
    
    public function getLayout(){
        if($this->layoutID != 0){
            return LayoutHandler::getInstance()->getStylesheet($this->layoutID);
        }
        
        return '';
    }
    
    public function isVisible(){
        if($this->invisible == 0 && $this->getPermission('canViewPage')) {
            return true;
        }
        return false;
    }
    
    public function isAccessible(){
        if($this->getPermission('canEnterPage')) return true; 
        return false;
    }
    
    public function isChild(){
        if($this->parentID == 0) return false;
        return true;
    }
    
    public function hasMenuItem(){
        $menuItem = @unserialize($this->menuItem);
        if(isset($menuItem['has']) && $menuItem['has'] != 0) return true;
        return false;
    }
    
    public function getParentPage(){
       if($this->isChild()){
            return new Page($this->parentID);
       }
       return null;
    }
    
    public function getParentPages(){
        if($this->isChild()){
            $parentPages = array();
            $parent = $this;
            while ($parent = $parent->getParentPage()) {
				$parentPages[] = $parent;
			}
            $parentPages = array_reverse($parentPages);
            return $parentPages;
        }
        return array();
    }
    
    public function getContentList(){
        $this->contentList =  new PageContentList($this->pageID);
        $this->contentList->readObjects();
        return $this->contentList->getObjects();
    }
    
    public function getPermission($permission = 'canViewPage') {
		return PagePermissionHandler::getInstance()->getPermission($this->pageID, $permission);
	}
    
    public function checkPermission(array $permissions = array('canViewPage')) {
		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}
}