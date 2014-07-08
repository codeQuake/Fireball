<?php
namespace cms\data\page;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DrainedPageNodeTree extends PageNodeTree {

	public $drainedID = null;

	public function __construct($parentID = null, $drainedID = null) {
		$this->drainedID = $drainedID;
		$this->parentID = $parentID;
	}

	public function isIncluded(PageNode $pageNode) {
		if ($pageNode->pageID == $this->drainedID) return false;
		return parent::isIncluded($pageNode);
	}
}
