<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentCache;
use cms\data\content\DrainedContentNodeTree;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * Use another content as content
 * @author	Florian Gail
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-text';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$templateName
	 */
	public $templateName = 'contentContentType';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		WCF::getTPL()->assign(array(
			'contentNodeTree' => new DrainedContentNodeTree()
		));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		/** @var Content $outputContent */
		$outputContent = ContentCache::getInstance()->getContent($content->contentID);
		if ($outputContent === null)
			return '';

		return $outputContent->getOutput();
	}

	/**
	 * @see \cms\system\content\type\IContentType::getSortableOutput()
	 */
	public function getSortableOutput(Content $content) {
		return 'Content #' . $content->contentID;
	}
}
