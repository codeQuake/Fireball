<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-code';

	/**
	 * @see	\cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {
		if (!isset($data['text']) || empty($data['text'])) {
			throw new UserInputException('text');
		}

		// check template code
		try {
			WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template', $data['text'], array(), true);
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
		$data = $content->handleContentData();
		$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template' . $content->contentID, $data['text']);
		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
