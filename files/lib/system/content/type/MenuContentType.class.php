<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\page\AccessiblePageNodeTree;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class MenuContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-sitemap';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		return 'menuContentType';
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$data = $content->handleContentData();

		switch ($data['type']) {
			case "children":
				$menuItems = $content->getPage()->getChildrenTree(isset($data['depth']) && $data['depth'] != 0 ? intval($data['depth']) - 1 : null);
			break;

			case "all":
				$nodeTree = new AccessiblePageNodeTree();
				$menuItems = $nodeTree->getIterator();
				if (isset($data['depth']) && $data['depth'] != 0) $menuItems->setMaxDepth(intval($data['depth']) - 1);
			break;
		}

		WCF::getTPL()->assign(array(
			'menuItems' => $menuItems,
			'data' => $data
		));

		return WCF::getTPL()->fetch('menuContentType', 'cms');
	}

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getCSSClasses()
	 */
	public function getCSSClasses() {
		return 'menuContainer';
	}
}
