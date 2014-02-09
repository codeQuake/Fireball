<?php
namespace cms\system\counter;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */
 
class VisitCountHandler extends SingletonFactory{
    public $session = null;
    
    public function init(){
        $this->session = WCF::getSession();
    }
}