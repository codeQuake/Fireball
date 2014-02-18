<?php
namespace cms\data\folder;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class FolderAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\folder\FolderEditor';
    protected $permissionsDelete = array('admin.cms.file.canAddFolder');
    protected $requireACP = array('delete');
    
    
}