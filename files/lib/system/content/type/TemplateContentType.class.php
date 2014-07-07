<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class TemplateContentType extends AbstractContentType {

	protected $icon = 'icon-sign-blank';

	public $objectType = 'de.codequake.cms.content.type.template';

	public function getFormTemplate() {
		return 'templateContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$compiled = WCF::getTPL()->getCompiler()->compileString($this->objectType . $content->contentID, $data['text']);
		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
