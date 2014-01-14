<?php
namespace cms\data\stylesheet;
use cms\data\layout\Layout;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class LayoutStylesheetList extends ViewableStylesheetList{
    
    public $layoutID = 0;
    
    public function __construct($layoutID){
        $this->layoutID = $layoutID;
        $layout = new Layout($this->layoutID);
        $data = @unserialize($layout->data);
        parent::__construct();
        $this->getConditionBuilder()->add('stylesheet.sheetID IN (?)', array($data));
        
    }
}