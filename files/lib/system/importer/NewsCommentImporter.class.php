<?php
namespace cms\system\importer;

use cms\data\entry\EntryEditor;
use cms\data\news\News;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCommentImporter;
use wcf\system\importer\ImportHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentImporter extends AbstractCommentImporter {
	protected $objectTypeName = 'de.codequake.cms.news.comment';

	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.comment.commentableContent', 'de.codequake.cms.news.comment');
		$this->objectTypeID = $objectType->objectTypeID;
	}

	/**
	 *
	 * @see wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = array()) {
		$data['objectID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.news', $data['objectID']);
		if (! $data['objectID']) return 0;
		
		$data['userID'] = ImportHandler::getInstance()->getNewID('com.woltlab.wcf.user', $data['userID']);
		
		return parent::import($oldID, $data);
	}
}
