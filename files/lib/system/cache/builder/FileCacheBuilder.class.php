<?php
namespace cms\system\cache\builder;

use cms\data\file\FileList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches files and file to category assignments.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	public function rebuild(array $parameters) {
		$data = [
			'files' => [],
			'filesToCategory' => []
		];

		$list = new FileList();
		$list->readObjects();
		foreach ($data['files'] = $list->getObjects() as $file) {
			foreach ($file->getCategoryIDs() as $categoryID) {
				$data['filesToCategory'][$file->fileID] = $categoryID;
			}
		}

		return $data;
	}
}
