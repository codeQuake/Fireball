<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class MenuContentType extends AbstractStructureContentType {

	protected $icon = 'icon-sitemap';

	public $objectType = 'de.codequake.cms.content.type.menu';

	public function getFormTemplate() {
		return 'menuContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		switch ($data['type']) {
			case "children":
				$menuItems = $content->getPage()->getChildrenTree();
				break;
		}
		WCF::getTPL()->assign(array(
			'menuItems' => $menuItems
		));
		return WCF::getTPL()->fetch('menuContentTypeOutput', 'cms');
	}

	public function getCSSClasses() {
		return 'menuContainer';
	}

	public function getChildCSSClasses(Content $content) {
		return '';
	}
}
