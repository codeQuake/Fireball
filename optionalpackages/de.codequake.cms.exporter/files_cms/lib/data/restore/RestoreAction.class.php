<?php
namespace cms\data\restore;
use wcf\data\AbstractDatabaseObjectAction;
use cms\data\folder\Folder;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class RestoreAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\restore\RestoreEditor';
    protected $requireACP = array('delete');
    
    
    
}