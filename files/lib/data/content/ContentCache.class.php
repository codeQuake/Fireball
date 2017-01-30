<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the content cache.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentCache extends SingletonFactory {

	protected $contents = [];

	protected $tree = [];

	protected function init() {
		$this->tree = ContentCacheBuilder::getInstance()->getData([], 'tree');
		$this->contents = ContentCacheBuilder::getInstance()->getData([], 'contents');
	}

	public function getContent($id) {
		if (isset($this->contents[$id])) return $this->contents[$id];
		return null;
	}

	public function getChildIDs($parentID = null) {
		if ($parentID === null) $parentID = '';
		
		if (!isset($this->tree[$parentID])) return [];
		
		return $this->tree[$parentID];
	}
}
