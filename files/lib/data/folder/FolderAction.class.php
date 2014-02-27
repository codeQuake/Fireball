<?php
namespace cms\data\folder;
use wcf\data\AbstractDatabaseObjectAction;
use cms\data\file\File;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class FolderAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\folder\FolderEditor';
    protected $permissionsDelete = array('admin.cms.file.canAddFile');
    protected $requireACP = array('delete');
    
    public function delete(){
        //del folder
        foreach($this->objectIDs as $objectID){
            $folder = new Folder($objectID);
            
            //fuck up all files
            foreach($folder->getFiles() as $file){
                unlink(CMS_DIR.'files/'.$folder->folderPath.'/'.$file->filename);
            }
            //delete folder
            rmdir(CMS_DIR.'files/'.$folder->folderPath);
        }
        parent::delete();
    }
}