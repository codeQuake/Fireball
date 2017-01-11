<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-code';
	
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$previewFields
	 */
	protected $previewFields = array('text');

	/**
	 * @see	\cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {
		if (!isset($data['text']) || empty($data['text'])) {
			throw new UserInputException('text');
		}
		
		// check template code
		try {
			$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template', $data['text'], array(), true);
			
			// cache compiled template with content
			RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;
		}
		catch (SystemException $e) {
			WCF::getTPL()->assign(array(
				'compileError' => $e->_getMessage()
			));

			throw new UserInputException('text', 'compileError');
		}
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$compiled = $content->compiled;
		if (empty($compiled[WCF::getLanguage()->languageCode])) {
			try {
				$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template' . $content->contentID, $content->text);

				$contentData = $content->contentData;
				if (!is_array($contentData)) $contentData = unserialize($contentData);
				$contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;

				$contentAction = new ContentAction(array($content), 'update',
					array('data' => array('contentData' => $contentData)));
				$contentAction->executeAction();
			}
			catch (SystemException $e) {
				if ($content->getPermission('mod.canViewErroredContent')) {
					$url = LinkHandler::getInstance()->getLink('ContentEdit', array('application' => 'cms', 'object' => $content, 'isACP' => true));
					return '<div class="error">Please check <a href="' . $url . '">content #' . $content->contentID . '</a> (language: ' . WCF::getLanguage()->languageCode . '). The following error occurred fetching the feed from <span class="inlineCode">' . $content->url . '</span>:<br><br>' . $e->getMessage() . '</div>';
				} else {
					return '';
				}
			}
		} else {
			$compiled = $content->compiled[WCF::getLanguage()->languageCode];
		}
		
		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
