<?php
namespace cms\data\stylesheet;
use wcf\system\request\IRouteController;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;

class Stylesheet extends CMSDatabaseObject implements IRouteController{

    protected static $databaseTableName = 'stylesheet';
    protected static $databaseTableIndexName = 'sheetID';
    
    
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