<?php

namespace cms\system\importer;
use cms\data\file\File;
use cms\data\file\FileAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;
use wcf\util\FileUtil;

/**
 * Provides an importer for files
 *
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileImporter extends AbstractImporter {
	/**
	 * @see	\wcf\system\importer\AbstractImporter::$className
	 */
	protected $className = 'cms\data\file\File';

	/**
	 * @see	\wcf\system\importer\IImporter::import()
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		unset($data['fileID']);

		if (is_numeric($oldID)) {
			$file = new File($oldID);
			if (!$file->fileID)
				$data['fileID'] = $oldID;
		}

		$categoryIDs = [];
		if (!empty($additionalData['categoryIDs'])) {
			foreach ($additionalData['categoryIDs'] as $categoryID) {
				$categoryIDs[] = ImportHandler::getInstance()->getNewID('de.codequake.cms.file.category', $categoryID);
			}
		}

		$action = new FileAction([], 'create', [
			'data' => $data
		]);
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->fileID;
		$file = new File($newID);

		if (!empty($categoryIDs)) {
			$updateAction = new FileAction([$file], 'update', ['categoryIDs' => $categoryIDs]);
			$updateAction->executeAction();
		}

		$dir = dirname($file->getLocation());
		if (!@file_exists($dir)) {
			FileUtil::makePath($dir, 0777);
		}

		// copy file
		try {
			if (!copy($additionalData['fileLocation'], $file->getLocation()))
				throw new SystemException();
		}
		catch (SystemException $e) {
			$deleteAction = new FileAction([$file], 'delete');
			$deleteAction->executeAction();
			return 0;
		}

		ImportHandler::getInstance()->saveNewID('de.codequake.cms.file', $oldID, $file->fileID);

		return $file->fileID;
	}
}
