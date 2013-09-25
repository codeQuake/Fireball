<?php
namespace cms\data\content;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;
use cms\data\page\Page;

class Content extends CMSDatabaseObject{
    protected static $databaseTableName = 'content';
    protected static $databaseTableIndexName = 'contentID';

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
    
    public function getPage(){
        return new Page($this->pageID);
    }
    
    public function getTitle(){
        return $this->title;
    }
    
}