<?php
namespace cms\system\cache\builder;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;
use cms\data\page\PageList;


/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCacheBuilder extends AbstractCacheBuilder{


    public function rebuild(array $parameters){
        $data = array(
            'pages' => array(),
            'aliasToID' => array()
        );
        
        $list = new PageList();
        $list->readObjects();
        $data['pages'] = $list->getObjects();
        foreach($data['pages'] as $page){
            $data['aliasToID'][$page->getAlias()] = $page->pageID;
        }
        
        return $data;
    }
}