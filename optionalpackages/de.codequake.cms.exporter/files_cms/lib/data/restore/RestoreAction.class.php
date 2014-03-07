<?php
namespace cms\data\restore;
use wcf\data\AbstractDatabaseObjectAction;
use cms\system\export\CMSImportHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class RestoreAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\restore\RestoreEditor';
    protected $requireACP = array('delete');
    protected $permissionsDelete = array('admin.cms.restore.canRestore');
    
    public function delete(){
        //del files
        foreach($this->objectIDs as $objectID){
            $restore = new Restore($objectID);
            unlink($restore->filename);
            
        }
        parent::delete();
    }
    
    public function import(){
        $objectID = reset($this->objectIDs);
        $restore = new Restore($objectID);
        
        CMSImportHandler::getInstance()->handleImport($restore->filename);
    }
    
    
}