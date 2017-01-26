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
	 * @inheritDoc
	 */
	protected $icon = 'icon-file-text-alt';

	/**
	 * @inheritDoc
	 */
	public $templateName = 'contentContentType';

	/**
	 * @inheritDoc
	 */
	public function getFormTemplate() {
		WCF::getTPL()->assign([
			'contentNodeTree' => new DrainedContentNodeTree()
		]);

		return parent::getFormTemplate();
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		/** @var Content $outputContent */
		$outputContent = ContentCache::getInstance()->getContent($content->contentData['contentID']);
		if ($outputContent === null)
			return '';

		return $outputContent->getOutput();
	}

	/**
	 * @inheritDoc
	 */
	public function getSortableOutput(Content $content) {
		return 'Content #' . $content->contentID;
	}
}
