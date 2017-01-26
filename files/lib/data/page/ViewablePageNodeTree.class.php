<?php
namespace cms\data\page;

/**
 * Generates a tree of all viewable pages. Pages are considered as viewable
 * when they are set as visible or the current user has the permission to see
 * invisible pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ViewablePageNodeTree extends PageNodeTree {
	/**
	 * @inheritDoc
	 */
	protected function isIncluded(PageNode $pageNode) {
		if ($pageNode->invisible) {
			return false;
		}

		return parent::isIncluded($pageNode);
	}
}
