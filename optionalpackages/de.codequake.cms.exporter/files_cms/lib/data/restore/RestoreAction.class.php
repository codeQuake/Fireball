<?php
namespace cms\data\restore;
use cms\system\export\CMSImportHandler;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class RestoreAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\restore\RestoreEditor';
    protected $requireACP = array('delete', 'importBackup');
    protected $permissionsDelete = array('admin.cms.restore.canRestore');
    protected $permissionsImportBackup = array('admin.cms.restore.canRestore');
    
    public function delete(){
        //del files
        foreach($this->objectIDs as $objectID){
            $restore = new Restore($objectID);
            unlink($restore->filename);
            
        }
        parent::delete();
    }
    
    public function validateImportBackup(){
        //does nothing
    }
    
    public function importBackup(){
        $objectID = reset($this->objectIDs);
        $restore = new Restore($objectID);
        
        CMSImportHandler::getInstance()->handleImport($restore->filename);
    }
    
    
}