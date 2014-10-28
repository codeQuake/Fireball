<?php
namespace cms\system\content\type;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\WCF;

/**
 * Dashboard content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DashboardContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-dashboard';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormOutput()
	 */
	public function getFormOutput() {
		WCF::getTPL()->assign(array(
			'boxList' => DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes')
		));

		return parent::getFormOutput();
	}
}
