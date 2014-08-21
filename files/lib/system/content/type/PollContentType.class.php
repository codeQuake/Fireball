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
class PollContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-bar-chart';

	public function getFormTemplate() {
		return 'pollContentType';
	}

	public function getOutput(Content $content) {
		WCF::getTPL()->assign('poll', $content->getPoll());
		return WCF::getTPL()->fetch('poll', 'wcf');
	}
}
