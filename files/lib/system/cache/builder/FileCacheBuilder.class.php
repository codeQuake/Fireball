<?php
namespace cms\system\cache\builder;

use cms\data\file\FileList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$data = array(
			'files' => array(),
			'filesToCategory' => array()
		);

		$list = new FileList();
		$list->readObjects();
		foreach ($data['files'] = $list->getObjects() as $file){
			foreach ($file->getCategoryIDs() as $categoryID) {
				$data['filesToCategory'][$file->fileID] = $categoryID;
			}
		}

		return $data;
	}
}
