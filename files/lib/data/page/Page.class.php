<?php
namespace cms\data\page;

use cms\data\CMSDatabaseObject;
use wcf\system\WCF;

class Page extends CMSDatabaseObject{

    protected static $databaseTableName = 'page';
    protected static $databaseTableIndexName = 'pageID';
    public $contentList = null;
    
    const TYPE_ROOT = 0;
    const TYPE_PAGE = 1;
    
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
    
    public function isRoot(){
        if($this->contentType == self::TYPE_ROOT){
            return true;
        }
        return false;
    }
    
    public function isPage(){
        if($this->contentType == self::TYPE_PAGE){
            return true;
        }
        return false;
    }
    
    public function isVisible(){
        if($this->invisible == 0) {
            return true;
        }
        return false;
    }
    
}