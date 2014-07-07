<?php
namespace cms\acp\page;

use cms\data\page\PageList;
use cms\system\counter\VisitCountHandler;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StatsPage extends AbstractPage {

	public $templateName = 'stats';

	public $activeMenuItem = 'cms.acp.menu.link.cms.page.statistics';

	public $startDate = 0;

	public $endDate = 0;

	public $visits = array();

	public $browsers = array();

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

	public function readData() {
		parent::readData();
		// set dates
		if (isset($_POST['startDate'])) $this->startDate = strtotime($_POST['startDate']);
		if (isset($_POST['endDate'])) $this->endDate = strtotime($_POST['endDate']);
		if ($this->startDate == 0) $this->startDate = TIME_NOW - 604800;
		if ($this->endDate == 0) $this->endDate = TIME_NOW;
		
		// get stats
		$this->visits = VisitCountHandler::getInstance()->getVisitors($this->startDate, $this->endDate);
		$m = 0;
		foreach ($this->visits as $visit) {
			$tmp = @unserialize($visit['visitors']['browsers']);
			if (empty($tmp)) $tmp = array();
			foreach ($tmp as $key => $value) {
				$this->browsers[$key] = array(
					'visits' => isset($this->browsers[$key]['visits']) ? $this->browsers[$key]['visits'] + $value : $value,
					'percentage' => 0
				);
				$m = $m + $value;
			}
		}
		// calc percentages
		foreach ($this->browsers as $key => $browser) {
			$browser['percentage'] = round(($browser['visits'] / $m) * 100, 2);
			$this->browsers[$key] = $browser;
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

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'visits' => $this->visits,
			'browsers' => $this->browsers,
			'objects' => $this->usersOnlineList,
			'colors' => $this->colors,
			'pages' => $this->pages,
			'startDate' => $this->startDate,
			'endDate' => $this->endDate
		));
	}
}
