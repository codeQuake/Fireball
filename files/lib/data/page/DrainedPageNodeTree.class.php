<?php
namespace cms\data\page;

/**
 * Generates a filtered tree of all pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DrainedPageNodeTree extends PageNodeTree {

	public $drainedID = null;

	public function __construct($parentID = null, $drainedID = null) {
		$this->parentID = $parentID;
		$this->drainedID = $drainedID;
	}

	/**
	 * @inheritDoc
	 */
	public function isIncluded(PageNode $pageNode) {
		if ($pageNode->pageID == $this->drainedID) {
			return false;
		}

		return parent::isIncluded($pageNode);
	}
}
