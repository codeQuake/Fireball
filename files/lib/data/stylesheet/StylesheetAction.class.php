<?php
namespace cms\data\stylesheet;
use wcf\data\AbstractDatabaseObjectAction;
use cms\system\layout\LayoutHandler;
use cms\data\layout\LayoutList;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class StylesheetAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\stylesheet\StylesheetEditor';
    protected $permissionsDelete = array('admin.cms.style.canAddStylesheet');
    protected $requireACP = array('delete');
    
    public function delete(){
        parent::delete();
        
        //kill all layouts
        $layoutList = new LayoutList();
        $layoutList->readObjects();
        foreach($layoutList->getObjects() as $layout){
            LayoutHandler::getInstance()->deleteStylesheet($layout->layoutiD);
        }
    }
    
    public function update(){
        parent::update();
        
        //kill all layouts
        $layoutList = new LayoutList();
        $layoutList->readObjects();
        foreach($layoutList->getObjects() as $layout){
            LayoutHandler::getInstance()->deleteStylesheet($layout->layoutID);
        }
    }
}