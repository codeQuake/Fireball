<?php
namespace cms\data\file;
use wcf\data\AbstractDatabaseObjectAction;
use cms\data\folder\Folder;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class FileAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\file\FileEditor';
    protected $permissionsDelete = array('admin.cms.file.canAddFile');
    protected $requireACP = array('delete');
    
    public function delete(){
        //del files
        foreach($this->objectIDs as $objectID){
            $file = new File($objectID);
            if($file->folderID == 0) unlink(CMS_DIR.'files/'.$file->filename);
            else{
                $folder = new Folder($file->folderID);
                unlink(CMS_DIR.'files/'.$folder->folderPath.'/'.$file->filename);
            }
        }
        parent::delete();
    }
    
}