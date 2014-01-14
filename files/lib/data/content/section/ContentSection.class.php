<?php
namespace cms\data\content\section;
use cms\data\content\Content;
use cms\data\CMSDatabaseObject;
use wcf\system\WCF;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\object\type\ObjectType;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

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
    
    public function getPreview(){
        $this->objectType = ObjectTypeCache::getInstance()->getObjectType($this->sectionTypeID);
        return $this->objectType->getProcessor()->getPreview($this->sectionID);
    }
    
    public function getEditor(){
        return new ContentSectionEditor($this);
    }
    
    public function getContent(){
        return new Content($this->contentID);
    }
    
    public function getObjectType(){
        $type =  new ObjectType($this->sectionTypeID);
        return $type->objectType;
    }
    
    
}