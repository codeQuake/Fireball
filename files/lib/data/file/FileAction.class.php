<?php
namespace cms\data\file;

use cms\data\folder\Folder;
use wcf\data\AbstractDatabaseObjectAction;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class FileAction extends AbstractDatabaseObjectAction {
    protected $className = 'cms\data\file\FileEditor';
    protected $permissionsDelete = array(
        'admin.cms.file.canAddFile'
    );
    protected $requireACP = array(
        'delete'
    );

    public function delete()
    {
        // del files
        foreach ($this->objectIDs as $objectID) {
            $file = new File($objectID);
            if ($file->folderID == 0 && file_exists(CMS_DIR . 'files/' . $file->filename)) unlink(CMS_DIR . 'files/' . $file->filename);
            else {
                $folder = new Folder($file->folderID);
                if (file_exists(CMS_DIR . 'files/' . $folder->folderPath . '/' . $file->filename)) unlink(CMS_DIR . 'files/' . $folder->folderPath . '/' . $file->filename);
            }
        }
        parent::delete();
    }
}
