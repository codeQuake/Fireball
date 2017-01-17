<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentCache;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TabMenuContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'fa-list-alt';

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getCSSClasses()
	 */
	public function getCSSClasses() {
		return 'section tabMenuContainer';
	}

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getChildCSSClasses()
	 */
	public function getChildCSSClasses(Content $content) {
		return 'tabMenuContent';
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$childIDs = ContentCache::getInstance()->getChildIDs($content->contentID);
		$children = [];

		foreach ($childIDs as $childID) {
			$children[] = ContentCache::getInstance()->getContent($childID);
		}

		WCF::getTPL()->assign([
			'children' => $children
		]);

		return parent::getOutput($content);
	}

	/**
	 * @see \cms\system\content\type\IContentType::getSortableOutput()
	 */
	public function getSortableOutput(Content $content) {
		return '';
	}
}
