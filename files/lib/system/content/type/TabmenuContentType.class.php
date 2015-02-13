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
class TabmenuContentType extends AbstractStructureContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-list-alt';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$templateName
	 */
	public $templateName = 'tabMenuContentType';

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getCSSClasses()
	 */
	public function getCSSClasses() {
		return 'tabMenuContainer';
	}

	/**
	 * @see	\cms\system\content\type\AbstractStructureContentType::getChildCSSClasses()
	 */
	public function getChildCSSClasses(Content $content) {
		return 'tabMenuContent container containerPadding';
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$childIDs = ContentCache::getInstance()->getChildIDs($content->contentID);
		$children = array();

		foreach ($childIDs as $childID) {
			$children[] = ContentCache::getInstance()->getContent($childID);
		}

		WCF::getTPL()->assign(array(
			'children' => $children
		));

		return WCF::getTPL()->fetch('tabMenuContentType', 'cms');
	}
}
