<?php
namespace cms\system\sitemap;
use cms\data\page\PageNodeTree;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class CMSSitemapProvider implements ISitemapProvider{
    
    
    public function getTemplate(){
        
        $list = new PageNodeTree(0);
        
        WCF::getTPL()->assign(array('pageList' => $list->getIterator()));
        
        return WCF::getTPL()->fetch('cmsSitemap','cms');
    }
}
