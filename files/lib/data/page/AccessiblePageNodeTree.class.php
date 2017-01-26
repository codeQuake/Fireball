<?php
namespace cms\data\page;

/**
 * Generates a tree of accessible pages. Pages are considered as accessible
 * when they are viewable and the current user has the permission to access the
 * page.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class AccessiblePageNodeTree extends ViewablePageNodeTree {
	/**
	 * @inheritDoc
	 */
	protected function isIncluded(PageNode $pageNode) {
		if (!$pageNode->canRead()) {
			return false;
		}

		return parent::isIncluded($pageNode);
	}
}
