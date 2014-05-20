<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentCache;
use wcf\system\WCF;

/**
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class TabmenuContentType extends AbstractStructureContentType {

	public $objectType = 'de.codequake.cms.content.type.tabmenu';
	protected $icon = 'icon-list-alt';

	public function getFormTemplate() {
		return 'tabMenuContentType';
	}

	public function getCSSClasses() {
		return 'tabMenuContainer';
	}

	public function getChildCSSClasses(Content $content) {
		return 'tabMenuContent container containerPadding';
	}

	public function getOutput(Content $content) {
		$childIDs = ContentCache::getInstance()->getChildIDs($content->contentID);
		$children = array();

		foreach ($childIDs as $childID) {
			$children[] = ContentCache::getInstance()->getContent($childID);
		}

		WCF::getTPL()->assign(array('children' => $children));
		return WCF::getTPL()->fetch('tabMenuContentTypeOutput', 'cms');
	}
}
