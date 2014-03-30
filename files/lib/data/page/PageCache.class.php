<?php
namespace cms\data\page;
use wcf\system\SingletonFactory;
use cms\system\cache\builder\PageCacheBuilder;
/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
 
class PageCache extends SingletonFactory{

    protected $aliasToID = array();
    protected $pages = array();
    
    protected function init(){
        $this->aliasToID = PageCacheBuilder::getInstance()->getData(array(), 'aliasToID');
        $this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
    }
    
    public function getIDByAlias($alias){
        if(isset($this->aliasToID[$alias])) return $this->aliasToID[$alias];
        return 0;
    }
    
    public function getPage($id){
        if(isset($this->pages[$id])) return $this->pages[$id];
        return null;
    }
}
 