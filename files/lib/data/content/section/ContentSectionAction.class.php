<?php
namespace cms\data\content\section;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class ContentSectionAction extends AbstractDatabaseObjectAction implements ISortableAction{

    protected $className = 'cms\data\content\section\ContentSectionEditor';
    protected $permissionsDelete = array('admin.cms.content.canAddContentSection');
    protected $requireACP = array('delete', 'updatePosition');
    
    public $items = array();
    
    public function validateUpdatePosition(){
        WCF::getSession()->checkPermissions(array('admin.cms.content.canAddContentSection'));
        
        //check parameters
        if (!isset($this->parameters['data']['structure'])) {
            throw new SystemException("Missing 'structure' parameter.");
        }
        if (!is_array($this->parameters['data']['structure'])) {
            throw new SystemException("'structure' parameter is no array.");
        }
        
		$itemIDs = array();
        foreach ($this->parameters['data']['structure'] as $items) {
			$itemIDs = array_merge($itemIDs, $items);
		}
        
        //createList
        $list = new ContentSectionList();
        $list->getConditionBuilder()->add('content_section.sectionID IN (?)', array($itemIDs));
        $list->readObjects();
        $this->items = $list->getObjects();
        
        //check number of items
        if (count($items) != count($itemIDs)) {
			throw new UserInputException('structure');
		}
        
    }
    public function updatePosition(){
        $sql = "UPDATE cms".WCF_N."_content_section
                SET showOrder = ?
                WHERE sectionID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        WCF::getDB()->beginTransaction();
        foreach ($this->parameters['data']['structure'] as $parentSectionID => $sectionIDs) {
			foreach ($sectionIDs as $showOrder => $sectionID) {
				$this->items[$sectionID]->getEditor()->update(array(
					'showOrder' => $showOrder + 1
				));
			}
		}
        WCF::getDB()->commitTransaction();
    
    }
}