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
class PollContentType extends AbstractContentType {

	protected $icon = 'icon-bar-chart';

	public $objectType = 'de.codequake.cms.content.type.poll';

	public function getFormTemplate() {
		return 'pollContentType';
	}

	public function getOutput(Content $content) {
		WCF::getTPL()->assign('poll', $content->getPoll());
		return '<div>' . WCF::getTPL()->fetch('poll', 'wcf') . '</div>';
	}
}
