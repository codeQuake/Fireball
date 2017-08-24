<?php
namespace cms\data\page;

/**
 * Generates a filtered tree of all pages.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DrainedPageNodeTree extends PageNodeTree {

	public $drainedID = null;

	public function __construct($parentID = null, $drainedID = null) {
		parent::__construct($parentID);
		
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
