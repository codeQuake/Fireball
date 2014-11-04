<?php
namespace cms\system\content;
use wcf\system\WCF;

/**
 * Template content implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContent extends AbstractContent {
	/**
	 * @see	\cms\system\content\IContent::getOutput()
	 */
	public function getOutput() {
		$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template.'.$this->contentID, $this->text);
		return WCF::getTPL()->fetchString($compiled['template']);
	}
}
