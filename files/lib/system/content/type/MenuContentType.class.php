<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\page\AccessiblePageNodeTree;
use cms\data\page\PageCache;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class MenuContentType extends AbstractStructureContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-sitemap';

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		switch ($content->type) {
			case "children":
				if (!empty($content->pageID))
					$menuItems = PageCache::getInstance()->getPage($content->pageID)->getChildrenTree(($content->depth) ? intval($content->depth) - 1 : null);
				else
					$menuItems = $content->getPage()->getChildrenTree(($content->depth) ? intval($content->depth) - 1 : null);
			break;

			case "all":
				$nodeTree = new AccessiblePageNodeTree();
				$menuItems = $nodeTree->getIterator();
				if ($content->depth) $menuItems->setMaxDepth(intval($content->depth) - 1);
			break;
		}

		WCF::getTPL()->assign([
			'menuItems' => $menuItems
		]);

		return parent::getOutput($content);
	}

	/**
	 * @inheritDoc
	 */
	public function getCSSClasses() {
		return 'menuContainer';
	}
}
