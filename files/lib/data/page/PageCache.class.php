<?php
namespace cms\data\page;

use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the page cache.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCache extends SingletonFactory {
	protected $aliasToID = array();
	protected $pages = array();
	protected $tree = array();

	protected function init() {
		$this->tree = PageCacheBuilder::getInstance()->getData(array(), 'tree');
		$this->aliasToID = PageCacheBuilder::getInstance()->getData(array(), 'aliasToID');
		$this->pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
	}

	public function getIDByAlias($alias) {
		if (isset($this->aliasToID[$alias])) return $this->aliasToID[$alias];
		return 0;
	}

	public function getPage($id) {
		if (isset($this->pages[$id])) return $this->pages[$id];
		return null;
	}

	public function getChildIDs($parentID = null) {
		if ($parentID === null) $parentID = '';
		
		if (! isset($this->tree[$parentID])) return array();
		
		return $this->tree[$parentID];
	}
}
