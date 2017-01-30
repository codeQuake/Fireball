<?php
namespace cms\data\file;

use cms\system\cache\builder\FileCacheBuilder;
use wcf\system\category\CategoryHandler;
use wcf\system\SingletonFactory;

/**
 * Manages the file cache.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileCache extends SingletonFactory {

	protected $files = [];

	protected $filesToCategory = [];
	
	protected $categoryIDs = [];
	
	protected $categories = null;

	protected function init() {
		$this->filesToCategory = FileCacheBuilder::getInstance()->getData([], 'filesToCategory');
		$this->files = FileCacheBuilder::getInstance()->getData([], 'files');
	}

	public function getFile($id) {
		if (isset($this->files[$id])) return $this->files[$id];
		return null;
	}

	public function getCategoryIDs($id) {
		if (!empty($this->categoryIDs)) {
			foreach ($this->filesToCategory as $fileID => $categoryID) {
				if ($fileID == $id) $this->categoryIDs[] = $categoryID;
			}
		}
		return $this->categoryIDs;
	}
	
	public function getCategories($id) {
		if ($this->categories === null) {
			$this->categories = [];
			
			foreach ($this->getCategoryIDs($id) as $categoryID) {
				$this->categories[$categoryID] = CategoryHandler::getInstance()->getCategory($categoryID);
			}
		}
	}
}
