<?php

namespace cms\system\importer;
use cms\data\page\Page;
use cms\data\page\PageAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * Provides an importer for pages
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageImporter extends AbstractImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'cms\data\page\Page';
	
	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
		
		if (is_numeric($oldID)) {
			$page = new Page($oldID);
			if (!$page->pageID)
				$data['pageID'] = $oldID;
		}
		
		$action = new PageAction(array(), 'create', array(
			'data' => $data
		));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->pageID;
		$page = new Page($newID);
		
		ImportHandler::getInstance()->saveNewID('de.codequake.cms.page', $oldID, $page->pageID);
		
		return $page->pageID;
	}
}
