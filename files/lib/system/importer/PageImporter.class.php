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
		unset($data['pageID']);
		unset($data['menuItemID']);

		if (!empty($data['authorID']))
			$data['authorID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['authorID']) ?: null;
		if (!empty($data['lastEditorID']))
			$data['lastEditorID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['lastEditorID']) ?: null;
		if (!empty($data['parentID']))
			$data['parentID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.page', $data['parentID']) ?: null;
		
		if (isset($data['authorID']) && $data['authorID'] == 0)
			unset($data['authorID']);
		if (isset($data['lastEditorID']) && $data['lastEditorID'] == 0)
			unset($data['lastEditorID']);
		if (isset($data['parentID']) && $data['parentID'] == 0)
			unset($data['parentID']);
			
		$stylesheetIDs = array();
		if (!empty($additionalData['stylesheetIDs'])) {
			foreach ($additionalData['stylesheetIDs'] as $stylesheetID) {
				$stylesheetIDs[] = ImportHandler::getInstance()->getNewID('de.codequake.cms.stylesheet', $stylesheetID);
			}
			$additionalData['stylesheetIDs'] = $stylesheetIDs;
		}
		
		if (!empty($data['additionalData']) && is_array($data['additionalData'])) {
			$data['additionalData'] = serialize($data['additionalData']);
		}
		
		if (is_numeric($oldID)) {
			$page = new Page($oldID);
			if (!$page->pageID)
				$data['pageID'] = $oldID;
		}
		
		$action = new PageAction(array(), 'create', array_merge($additionalData, array(
			'data' => $data,
		)));
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->pageID;
		$page = new Page($newID);
		
		ImportHandler::getInstance()->saveNewID('de.codequake.cms.page', $oldID, $page->pageID);
		
		return $page->pageID;
	}
}
