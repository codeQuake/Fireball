<?php
namespace cms\data\page;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\UserInputException;

class PageAction extends AbstractDatabaseObjectAction implements ISortableAction{

    protected $className = 'cms\data\page\PageEditor';
    public $items = array();
    
    public function validateUpdatePosition() {
        if (!isset($this->parameters['data']) || !isset($this->parameters['data']['structure']) || !is_array($this->parameters['data']['structure'])) {
            throw new UserInputException('structure');
        }
    }
    public function UpdatePosition(){
        $sql = "UPDATE	cms".WCF_N."_page
			SET	parentID= ?,
				showOrder = ?
			WHERE	pageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'] as $parentID => $items) {
			foreach ($items as $showOrder => $pageID) {
				$statement->execute(array(
					($parentID ? $this->items[$parentID]->pageID : ''),
					$showOrder + 1,
					$pageID
				));
			}
		}
		WCF::getDB()->commitTransaction();
    }
}