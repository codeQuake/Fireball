<?php
namespace cms\system\revision;

use cms\system\cache\builder\ContentRevisionCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentRevisionHandler extends SingletonFactory {
	/**
	 * list of revisions grouped by content id.
	 * @var	array<array<\cms\data\content\Content>>
	 */
	protected $revisions = array();

	/**
	 * list of revision ids grouped by content id.
	 * @var	array<array<integer>>
	 */
	protected $revisionIDs = array();

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	public function init() {
		$this->revisions = ContentRevisionCacheBuilder::getInstance()->getData(array(), 'revisions');
		$this->revisionIDs = ContentRevisionCacheBuilder::getInstance()->getData(array(), 'revisionIDs');
	}

	/**
	 * Returns all revisions of the content with the given id.
	 * 
	 * @param	integer		$contentID
	 * @return	array<\cms\data\content\Content>
	 */
	public function getRevisions($contentID) {
		if (isset($this->revisions[$contentID])) {
			return $this->revisions[$contentID];
		}
		return array();
	}

	/**
	 * Returns the revision with the given id.
	 * 
	 * @param	integer		$contentID
	 * @param	integer		$revisionID
	 * @return	\cms\data\content\Content
	 */
	public function getRevisionByID($contentID, $revisionID) {
		if (isset($this->revisions[$contentID][$revisionID])) {
			return $this->revisions[$contentID][$revisionID];
		}
		return null;
	}
}
