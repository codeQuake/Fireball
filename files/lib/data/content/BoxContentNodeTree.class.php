<?php
namespace cms\data\content;

/**
 * ContentNodeTree containing the parent content as child of nulled-content
 * in order to be able to display the parent too (without code duplication)
 *
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class BoxContentNodeTree extends ContentNodeTree {
	public function buildTree() {
		$this->parentNode = new ContentNode(new Content(null, array('contentID' => 0)));
		$parentContent = $this->getNode($this->parentID);
		$this->parentNode->addChild($parentContent);
		$this->buildTreeLevel($parentContent);
	}
}
