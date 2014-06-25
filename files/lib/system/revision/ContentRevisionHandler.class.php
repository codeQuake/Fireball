<?php
namespace cms\system\revision;

use cms\system\cache\builder\ContentRevisionCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class ContentRevisionHandler extends SingletonFactory {
	protected $revisions = array();
	protected $revisionIDs = array();

	public function init() {
		$this->revisions = ContentRevisionCacheBuilder::getInstance()->getData(array(), 'revisions');
		$this->revisionIDs = ContentRevisionCacheBuilder::getInstance()->getData(array(), 'revisionIDs');
	}

	public function getRevisions($contentID) {
		if (isset($this->revisions[$contentID])) {
			return $this->revisions[$contentID];
		}
		return array();
	}

	public function getRevisionByID($contentID, $revisionID) {
		if (isset($this->revisions[$contentID][$revisionID])) {
			return $this->revisions[$contentID][$revisionID];
		}
		return null;
	}
}
