<?php
namespace cms\data\news\image;
use wcf\data\AbstractDatabaseObjectAction;


/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class NewsImageAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\file\FileEditor';
    protected $permissionsDelete = array('admin.cms.news.canManageCategory');
    protected $requireACP = array('delete');
   
    
}