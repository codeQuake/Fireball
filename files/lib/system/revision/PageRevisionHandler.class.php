<?php
namespace cms\system\revision;

use cms\system\cache\builder\PageRevisionCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PageRevisionHandler extends SingletonFactory {
	protected $revisions = array();
	protected $revisionIDs = array();

	public function init() {
		$this->revisions = PageRevisionCacheBuilder::getInstance()->getData(array(), 'revisions');
		$this->revisionIDs = PageRevisionCacheBuilder::getInstance()->getData(array(), 'revisionIDs');
	}

	public function getRevisions($pageID) {
		if (isset($this->revisions[$pageID])) {
			return $this->revisions[$pageID];
		}
		return array();
	}

	public function getRevisionByID($pageID, $revisionID) {
		if (isset($this->revisions[$pageID][$revisionID])) {
			return $this->revisions[$pageID][$revisionID];
		}
		return null;
	}
}
