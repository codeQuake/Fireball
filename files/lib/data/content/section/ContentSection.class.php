<?php
namespace cms\data\content\section;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;
use cms\data\content\Content;

class ContentSection extends CMSDatabaseObject{
    protected static $databaseTableName = 'content_section';
    protected static $databaseTableIndexName = 'sectionID';

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
    
    public function getContent(){
        return new Content($this->contentID);
    }
    
    
}