<?php
namespace cms\acp\page;

use cms\data\page\PageList;
use cms\system\counter\VisitCountHandler;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 * Shows the stats page.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StatsPage extends AbstractPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.statistics';

	public $startDate = 0;

	public $endDate = 0;

	public $visits = array();

	public $browsers = array();

	public $platforms = array();

	public $devices = array();

	public $colors = array(
		'#015294',
		'#F7464A',
		'#E2EAE9',
		'#D4CCC5',
		'#949FB1',
		'#4D5360',
		'#F38630',
		'#f0f0f0',
		'#1f1f1'
	);

	public $pages = null;

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// set dates
		if (isset($_POST['startDate'])) $this->startDate = strtotime($_POST['startDate']);
		if (isset($_POST['endDate'])) $this->endDate = strtotime($_POST['endDate']);
		if ($this->startDate == 0) $this->startDate = TIME_NOW - 604800;
		if ($this->endDate == 0) $this->endDate = TIME_NOW;

		// get stats
		$this->visits = VisitCountHandler::getInstance()->getVisitors($this->startDate, $this->endDate);

		foreach ($this->visits as $visit) {
			$browsers = @unserialize($visit['visitors']['browsers']);
			if (empty($browsers)) $browsers = array();
			foreach ($browsers as $key => $value) {
				$this->browsers[$key] = array(
					'visits' => isset($this->browsers[$key]['visits']) ? $this->browsers[$key]['visits'] + $value : $value
				);
			}

			$platforms = @unserialize($visit['visitors']['platforms']);
			if (empty($platforms)) $platforms = array();
			foreach ($platforms as $key => $value) {
				$this->platforms[$key] = array(
					'visits' => isset($this->platforms[$key]['visits']) ? $this->platforms[$key]['visits'] + $value : $value
				);
			}

			$devices = @unserialize($visit['visitors']['devices']);
			if (empty($devices)) $devices = array();
			foreach ($devices as $key => $value) {
				$this->devices[$key] = array(
					'visits' => isset($this->devices[$key]['visits']) ? $this->devices[$key]['visits'] + $value : $value
				);
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
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'visits' => $this->visits,
			'browsers' => $this->browsers,
			'platforms' => $this->platforms,
			'devices' => $this->devices,
			'objects' => $this->usersOnlineList,
			'colors' => $this->colors,
			'pages' => $this->pages,
			'startDate' => $this->startDate,
			'endDate' => $this->endDate
		));
	}
}
