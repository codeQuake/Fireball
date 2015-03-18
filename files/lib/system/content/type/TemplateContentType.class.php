<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentEditor;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
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
			RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData['compiled'] = $compiled;
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
		if (!$content->compiled) {
			$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template' . $content->contentID, $content->text);
		} else {
			$compiled = $content->compiled;
		}

		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
