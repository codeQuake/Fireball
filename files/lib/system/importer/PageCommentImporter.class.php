<?php

namespace cms\system\importer;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCommentImporter;
use wcf\system\importer\ImportHandler;

/**
 * Provides an importer for comments
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentImporter extends AbstractCommentImporter {
	/**
	 * @see	\wcf\system\importer\AbstractCommentImporter::$objectTypeName
	 */
	protected $objectTypeName = 'de.codequake.cms.page.comment';
	
	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', $this->objectTypeName);
		$this->objectTypeID = $objectType->objectTypeID;
	}
	
	/**
	 * @see wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.page', $data['objectID']);
		if (!$data['objectID'])
			return 0;
		
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
		
		return parent::import($oldID, $data);
	}
}
