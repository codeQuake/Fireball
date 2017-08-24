<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\system\template\EnvironmentTemplateEngine;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-code';
	
	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['text'];

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
		if (!isset($data['text']) || empty($data['text'])) {
			throw new UserInputException('text');
		}
		
		// check template code
		try {
			$test = EnvironmentTemplateEngine::getInstance();
			
			$test->setEnvironment('user');
			$compiled = $test->getCompiler()->compileString('de.codequake.cms.content.type.template', $data['text'], [], true);
			$test->setEnvironment('admin');
			
			// cache compiled template with content
			RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;
		}
		catch (SystemException $e) {
			WCF::getTPL()->assign([
				'compileError' => $e->getMessage()
			]);

			throw new UserInputException('text', 'compileError');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		$compiled = $content->compiled;
		if (empty($compiled[WCF::getLanguage()->languageCode])) {
			try {
				$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template' . $content->contentID, $content->text);

				$contentData = $content->contentData;
				if (!is_array($contentData)) {
					/** @noinspection PhpParamsInspection */
					$contentData = unserialize($contentData);
				}
				$contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;

				$contentAction = new ContentAction([$content], 'update', ['data' => ['contentData' => $contentData]]);
				$contentAction->executeAction();
			}
			catch (SystemException $e) {
				if ($content->getPermission('mod.canViewErroredContent')) {
					$url = LinkHandler::getInstance()->getLink('ContentEdit', ['application' => 'cms', 'object' => $content, 'isACP' => true]);
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
