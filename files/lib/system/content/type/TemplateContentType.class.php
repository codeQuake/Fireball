<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-sign-blank';

	public function getFormTemplate() {
		return 'templateContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$compiled = WCF::getTPL()->getCompiler()->compileString($this->objectType . $content->contentID, $data['text']);
		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
