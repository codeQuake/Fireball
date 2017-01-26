<?php
namespace cms\acp\page;

use cms\data\page\PageList;
use cms\system\counter\VisitCountHandler;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Shows the stats page.
 *
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StatsPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.page.statistics';

	/**
	 * list of browsers
	 * @var	array
	 */
	public $browsers = [];

	/**
	 * colors for the graphs
	 * @var	array
	 */
	public $colors = [
		'#015294',
		'#F7464A',
		'#E2EAE9',
		'#D4CCC5',
		'#949FB1',
		'#4D5360',
		'#F38630',
		'#f0f0f0',
		'#1f1f1'
	];

	/**
	 * list of devices
	 * @var	array
	 */
	public $devices = [];

	/**
	 * end date
	 * @var	integer
	 */
	public $endDate = 0;

	/**
	 * @inheritDoc
	 */
	public $neededModules = ['FIREBALL_PAGES_ENABLE_STATISTICS'];

	/**
	 * list of most viewed pages
	 * @var	array<\cms\data\page\Page>
	 */
	public $pages = null;

	/**
	 * list of platforms
	 * @var	array
	 */
	public $platforms = [];

	/**
	 * start date
	 * @var	integer
	 */
	public $startDate = 0;

	/**
	 * list of visits
	 * @var	array
	 */
	public $visits = [];

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// set dates
		if (isset($_POST['startDate']) && $_POST['startDate'] > 0 && $_POST['startDate'] != '') $this->startDate = \DateTime::createFromFormat('Y-m-d', $_POST['startDate'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['endDate']) && $_POST['endDate'] > 0 && $_POST['endDate'] != '') $this->endDate = \DateTime::createFromFormat('Y-m-d', $_POST['endDate'], WCF::getUser()->getTimeZone())->getTimestamp();
		if ($this->startDate == 0) $this->startDate = TIME_NOW - 604800;
		if ($this->endDate == 0) $this->endDate = TIME_NOW;

		// get stats
		$this->visits = VisitCountHandler::getInstance()->getVisitors($this->startDate, $this->endDate);

		foreach ($this->visits as $visit) {
			$browsers = @unserialize($visit['visitors']['browsers']);
			if (empty($browsers)) $browsers = [];

			foreach ($browsers as $key => $value) {
				$this->browsers[$key] = [
					'visits' => isset($this->browsers[$key]['visits']) ? $this->browsers[$key]['visits'] + $value : $value
				];
			}

			$platforms = @unserialize($visit['visitors']['platforms']);
			if (empty($platforms)) $platforms = [];

			foreach ($platforms as $key => $value) {
				$this->platforms[$key] = [
					'visits' => isset($this->platforms[$key]['visits']) ? $this->platforms[$key]['visits'] + $value : $value
				];
			}

			$devices = @unserialize($visit['visitors']['devices']);
			if (empty($devices)) $devices = [];

			foreach ($devices as $key => $value) {
				$this->devices[$key] = [
					'visits' => isset($this->devices[$key]['visits']) ? $this->devices[$key]['visits'] + $value : $value
				];
			}
		}

		// read pages
		$list = new PageList();
		$list->sqlOrderBy = 'page.clicks DESC';
		$list->sqlLimit = '8';
		$list->readObjects();
		$this->pages = $list->getObjects();

		// user online list
		$this->usersOnlineList = new UsersOnlineList();
		$this->usersOnlineList->readStats();
		$this->usersOnlineList->readObjects();
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		$startDate = DateUtil::getDateTimeByTimestamp($this->startDate);
		$startDate->setTimezone(WCF::getUser()->getTimeZone());
		$startDate = $startDate->format('Y-m-d');

		$endDate = DateUtil::getDateTimeByTimestamp($this->endDate);
		$endDate->setTimezone(WCF::getUser()->getTimeZone());
		$endDate = $endDate->format('Y-m-d');

		WCF::getTPL()->assign([
			'visits' => $this->visits,
			'browsers' => $this->browsers,
			'platforms' => $this->platforms,
			'devices' => $this->devices,
			'objects' => $this->usersOnlineList,
			'colors' => $this->colors,
			'pages' => $this->pages,
			'startDate' => $startDate,
			'endDate' => $endDate
		]);
	}
}
