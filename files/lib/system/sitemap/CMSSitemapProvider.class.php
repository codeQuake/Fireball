<?php
namespace cms\system\sitemap;
use cms\data\page\PageList;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class CMSSitemapProvider implements ISitemapProvider{
    
    
    public function getTemplate(){
        $list = new PageList();
        $list->getConditionBuilder()->add('page.parentID = ?', array(0));
        $list->readObjects();
        $list = $list->getObjects();
        
        WCF::getTPL()->assign(array('pageList' => $list));
        
        return WCF::getTPL()->fetch('cmsSitemap','cms');
    }
}