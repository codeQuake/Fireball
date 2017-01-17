<?php
namespace cms\system\importer;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractACLImporter;
use wcf\system\importer\ImportHandler;

/**
 * Imports ACLs of pages.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageACLImporter extends AbstractACLImporter {
	/**
	 * @see	\wcf\system\importer\AbstractACLImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.codequake.cms.page';
	
	/**
	 * Creates a new ACLImporter object.
	 */
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.acl', $this->objectTypeName);
		$this->objectTypeID = $objectType->objectTypeID;
		
		parent::__construct();
	}
	
	/**
	 * @see	\wcf\system\importer\AbstractACLImporter::import()
	 * 
	 * this has been copied from AbstractACLImporter (written by WoltLab GmbH)
	 * the objecttype of imported acls has been chenged unless this would crash with page imports
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		if (!isset($this->options[$additionalData['optionName']])) return 0;
		$data['optionID'] = $this->options[$additionalData['optionName']];
		
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.page.acl', $data['objectID']);
		if (!$data['objectID']) return 0;
		
		if (!empty($data['groupID'])) {
			$data['groupID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user.group', $data['groupID']);
			if (!$data['groupID']) return 0;
			
			$sql = "INSERT IGNORE INTO	wcf".WCF_N."_acl_option_to_group
							(optionID, objectID, groupID, optionValue)
				VALUES		        (?, ?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$data['optionID'], $data['objectID'], $data['groupID'], $data['optionValue']]);
			
			return 1;
		}
		else if (!empty($data['userID'])) {
			$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
			if (!$data['userID']) return 0;
			
			$sql = "INSERT IGNORE INTO	wcf".WCF_N."_acl_option_to_user
							(optionID, objectID, userID, optionValue)
				VALUES		        (?, ?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$data['optionID'], $data['objectID'], $data['userID'], $data['optionValue']]);
			
			return 1;
		}
	}
}
