<?php

namespace cms\system\importer;
use cms\data\content\Content;
use cms\data\content\ContentAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * Provides an importer for contents
 *
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentImporter extends AbstractImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = Content::class;

	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		unset($data['contentID']);

		if (empty($data['dontUpdateParentID'])) {
			if (!empty($data['parentID']))
				$data['parentID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.content', $data['parentID']);
			if (isset($data['parentID']) && $data['parentID'] == 0)
				unset($data['parentID']);
		} else {
			unset($data['dontUpdateParentID']);
		}

		$data['pageID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.page', $data['pageID']);

		if (is_numeric($oldID)) {
			$content = new Content($oldID);
			if (!$content->contentID)
				$data['contentID'] = $oldID;
		}

		if (!empty($data['contentData']['pageID'])) {
			$data['contentData']['pageID'] = ImportHandler::getInstance()->getNewID('de.codequake.cms.page', $data['pageID']);
		}

		if (isset($data['contentData']) && is_array($data['contentData'])) {
			$data['contentData'] = serialize($data['contentData']);
		}

		if (isset($data['additionalData']) && is_array($data['additionalData'])) {
			$data['additionalData'] = serialize($data['additionalData']);
		}

		$action = new ContentAction([], 'create', [
			'data' => $data
		]);
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->contentID;
		$content = new Content($newID);

		ImportHandler::getInstance()->saveNewID('de.codequake.cms.content', $oldID, $content->contentID);

		return $content->contentID;
	}
}
