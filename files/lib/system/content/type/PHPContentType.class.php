<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PHPContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'fa-code';

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$previewFields
	 */
	protected $previewFields = ['text'];

	/**
	 * @see	\cms\system\content\type\AbstractContentType::$templateName
	 */
	public $templateName = 'phpContentType';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		try {
			$output = eval($content->text);
		}
		catch (\ParseError $e) {
			if ($content->getPermission('mod.canViewErroredContent')) {
				$url = LinkHandler::getInstance()->getLink('ContentEdit', ['application' => 'cms', 'object' => $content, 'isACP' => true]);
				$output = '<div class="error">';
				$output .= 'Please check <a href="' . $url . '">content #' . $content->contentID . '</a>. The following error occurred parsing this content at line ' . $e->getLine() . ':<br><br>';
				$output .= $e->getMessage();
				$output .= '</div>';
			}
			else {
				$output = '';
			}
		}

		return $output;
	}

	/**
	 * @see \cms\system\content\type\IContentType::getSortableOutput()
	 */
	public function getSortableOutput(Content $content) {
		return '';
	}
}
