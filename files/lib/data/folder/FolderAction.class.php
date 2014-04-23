<?php
namespace cms\data\folder;

use cms\data\file\FileAction;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes folder-related actions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FolderAction extends AbstractDatabaseObjectAction {
	protected $className = 'cms\data\folder\FolderEditor';
	protected $permissionsDelete = array(
		'admin.cms.file.canAddFile'
	);
	protected $requireACP = array(
		'delete'
	);

	public function delete() {
		// del folder
		foreach ($this->objectIDs as $objectID) {
			$folder = new Folder($objectID);
			
			// fuck up all files
			$action = new FileAction($folder->getFiles(), 'delete');
			$action->executeAction();
			
			// delete folder
			if (file_exists(CMS_DIR . 'files/' . $folder->folderPath)) rmdir(CMS_DIR . 'files/' . $folder->folderPath);
		}
		parent::delete();
	}
}
