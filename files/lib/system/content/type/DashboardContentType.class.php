<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class DashboardContentType extends AbstractContentType {

	protected $icon = 'icon-dashboard';

	public $objectType = 'de.codequake.cms.content.type.dashboard';

	public function getFormTemplate() {
		WCF::getTPL()->assign(array(
			'boxList' => DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes')
		));
		return 'dashboardContentType';
	}

	public function getOutput(Content $content) {
		return '';
	}
}
