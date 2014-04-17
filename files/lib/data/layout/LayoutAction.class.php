<?php
namespace cms\data\layout;
use cms\system\layout\LayoutHandler;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class LayoutAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\layout\LayoutEditor';
    protected $permissionsDelete = array('admin.cms.style.canAddLayout');
    protected $requireACP = array('delete');
    

    public function delete(){
        //delete css files
        foreach($this->objectIDs as $objectID){
            LayoutHandler::getInstance()->deleteStylesheet($objectID);
        }
        parent::delete();
    }
    
    public function update(){        
        parent::update();
        //delete css files
        foreach($this->objectIDs as $objectID){
            LayoutHandler::getInstance()->deleteStylesheet($objectID);
        }
    }
}
