<?php
namespace cms\data\feed;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.news.rss
 */

class FeedAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\feed\FeedEditor';
    protected $permissionsDelete = array('admin.cms.news.canAddFeed');
    protected $requireACP = array('delete');
    
   
}