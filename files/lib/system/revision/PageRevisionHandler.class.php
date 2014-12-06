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
	/**
	 * list of revisions grouped by page id.
	 * @var	array<array<\cms\data\page\Page>>
	 */
	protected $revisions = array();

	/**
	 * list of revision ids grouped by page id.
	 * @var	array<array<integer>>
	 */
	protected $revisionIDs = array();

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	public function init() {
		$this->revisions = PageRevisionCacheBuilder::getInstance()->getData(array(), 'revisions');
		$this->revisionIDs = PageRevisionCacheBuilder::getInstance()->getData(array(), 'revisionIDs');
	}

	/**
	 * Returns all revisions of the page with the given id.
	 * 
	 * @param	integer		$pageID
	 * @return	array<\cms\data\page\Page>
	 */
	public function getRevisions($pageID) {
		if (isset($this->revisions[$pageID])) {
			return $this->revisions[$pageID];
		}

		return array();
	}

	/**
	 * Returns the revision with the given id.
	 * 
	 * @param	integer		$pageID
	 * @param	integer		$revisionID
	 * @return	\cms\data\page\Page
	 */
	public function getRevisionByID($pageID, $revisionID) {
		if (isset($this->revisions[$pageID][$revisionID])) {
			return $this->revisions[$pageID][$revisionID];
		}

		return null;
	}
}
