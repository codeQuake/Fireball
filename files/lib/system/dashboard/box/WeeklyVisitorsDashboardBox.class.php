<?php
namespace cms\system\dashboard\box;

use cms\system\counter\VisitCountHandler;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractContentDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard content box viewing last 7 days visitors
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class WeeklyVisitorsDashboardBox extends AbstractContentDashboardBox{

	/**
	 * visitor array
	 * @var array<mixed>
	 */
	public $visits = array();

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		//visitors of the last 604800 sec = 1 week
		$this->visits = VisitCountHandler::getInstance()->getVisitors(TIME_NOW - 604800, TIME_NOW); 

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->visits)) return '';

		WCF::getTPL()->assign(array(
			'visits' => $this->visits
		));

		return WCF::getTPL()->fetch('dashboardBoxWeeklyVisitors', 'cms');
	}

}
