<?php
namespace cms\data\file;
use wcf\system\request\IRouteController;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;

class File extends CMSDatabaseObject implements IRouteController{

    protected static $databaseTableName = 'file';
    protected static $databaseTableIndexName = 'fileID';
    
    
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
}