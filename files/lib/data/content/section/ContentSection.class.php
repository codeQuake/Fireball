<?php
namespace cms\data\content\section;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;
use wcf\data\object\type\ObjectTypeCache;


class ContentSection extends CMSDatabaseObject{
    protected static $databaseTableName = 'content_section';
    protected static $databaseTableIndexName = 'sectionID';

    public $objectType = null;
    
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
    
    public function getOutput(){
        $this->objectType = ObjectTypeCache::getInstance()->getObjectType($this->sectionTypeID);
        return $this->objectType->getProcessor()->getOutput($this->sectionID);
    }
    
    
}