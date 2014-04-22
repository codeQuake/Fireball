<?php
namespace cms\system\counter;

use wcf\system\cache\builder\SpiderCacheBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class VisitCountHandler extends SingletonFactory {
	public $session = null;

	public function init() {
		$this->session = WCF::getSession();
	}

	protected function canCount() {
		if ($this->session->getVar('counted')) return false;
		return true;
	}

	public function count() {
		if ($this->canCount()) {
			$userID = WCF::getUser()->userID;
			$spider = $this->getSpiderID($this->session->userAgent);
			if ($spider === null) $spider = 0;
			$browser = $this->getBrowser($this->session->userAgent);
			$browserName = $browser['name'];
			
			// update
			if ($this->existingColumn()) {
				$sql = "SELECT * FROM cms" . WCF_N . "_counter WHERE day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . " AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . " AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute();
				$counter = $statement->fetchArray();
				
				$browsers = @unserialize($counter['browsers']);
				if (isset($browsers[$browserName])) $browsers[$browserName] = $browsers[$browserName] + 1;
				else
					$browsers[$browserName] = 1;
				$users = $counter['users'];
				if ($userID != 0) $users ++;
				$spiders = $counter['spiders'];
				if ($spider != 0) $spiders ++;
				$visits = $counter['visits'] + 1;
				
				$sql = "UPDATE cms" . WCF_N . "_counter 
                        SET visits = ?, users = ?, spiders = ?, browsers = ?
                        WHERE day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . " AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . " AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
					$visits,
					$users,
					$spiders,
					serialize($browsers)
				));
			}
			// create new
			else {
				$users = 0;
				$spiders = 0;
				if ($userID != 0) $users ++;
				if ($spider != 0) $spiders ++;
				$browsers = array();
				$browsers[$browserName] = 1;
				
				$sql = "INSERT INTO cms" . WCF_N . "_counter VALUES (?, ?, ?, ?, ?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j'),
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n'),
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y'),
					1,
					$users,
					$spiders,
					serialize($browsers)
				));
			}
			$this->session->register('counted', true);
		}
	}

	public function existingColumn() {
		$sql = "SELECT COUNT(*) AS amount FROM cms" . WCF_N . "_counter WHERE day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . " AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . " AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		;
		if ($statement->fetchColumn() != 0) return true;
		return false;
	}
	
	
	public function getVisitors($start, $end) {
		$vistors = array();
		$date = $start;
		while ($date <= $end) {
			
			$months = $this->getMonths();
			$visitors[] = array(
				'visitors' => $this->getDailyVisitors(DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'j'), DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'n'), DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'Y')),
				'string' => DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'j') . '. ' . $months[DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'n') - 1] . ' ' . DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'Y')
			);
			$date = $date + 86400;
		}
		return $visitors;
	}

	public function getAllVisitors() {
		$sql = "SELECT * FROM cms" . WCF_N . "_counter";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$count = 0;
		while ($row = $statement->fetchArray()) {
			$count = $count + $row['visits'];
		}
		return $count;
	}

	public function getDailyVisitors($day = 10, $month = 2, $year = 2014) {
		$sql = "SELECT * FROM cms" . WCF_N . "_counter WHERE day = ? AND month = ? AND year = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$day,
			$month,
			$year
		));
		return $statement->fetchArray();
	}

	public function getWeeklyVisitorArray() {
		$currentMonth = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n');
		$currentYear = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
		$currentDay = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j');
		
		$visitors = array();
		$year = $currentYear;
		$month = $currentMonth;
		$day = $currentDay;
		
		for ($i = 1; $i <= 7; $i ++) {
			$months = $this->getMonths();
			$visitors[$i] = array(
				'string' => $day . '. ' . $months[$month - 1] . ' ' . $year,
				'visitors' => $this->getDailyVisitors($day, $month, $year)
			);
			$day --;
			if ($day == 0) {
				$month --;
				if (in_array($month, array(
					1,
					3,
					5,
					7,
					8,
					10,
					12
				))) $day = 31;
				if ($month == 2) $day = 28;
				else
					$day = 30;
			}
			if ($month == 0) {
				$month = 12;
				$year = $currentYear - 1;
			}
		}
		return array_reverse($visitors);
	}

	public function getBrowser($u_agent = '') {
		if ($u_agent == '') return array(
			'userAgent' => '',
			'name' => 'unknown',
			'version' => '?',
			'platform' => '',
			'pattern' => ''
		);
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version = "";
		
		// First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		else if (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		else if (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
		
		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && ! preg_match('/Opera/i', $u_agent)) {
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		else if (preg_match('/Firefox/i', $u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		else if (preg_match('/Chrome/i', $u_agent)) {
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		else if (preg_match('/Safari/i', $u_agent)) {
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		else if (preg_match('/Opera/i', $u_agent)) {
			$bname = 'Opera';
			$ub = "Opera";
		}
		else if (preg_match('/Netscape/i', $u_agent)) {
			$bname = 'Netscape';
			$ub = "Netscape";
		}
		else {
			$ub = '';
		}
		
		// finally get the correct version number
		$known = array(
			'Version',
			$ub,
			'other'
		);
		$pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (! preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			// we will have two since we are not using 'other' argument yet
			// see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			}
			else {
				$version = isset($matches['version'][1]) ? $matches['version'][1] : "";
			}
		}
		else {
			$version = $matches['version'][0];
		}
		
		// check if we have a number
		if ($version == null || $version == "") {
			$version = "?";
		}
		
		return array(
			'userAgent' => $u_agent,
			'name' => $bname,
			'version' => $version,
			'platform' => $platform,
			'pattern' => $pattern
		);
	}

	protected function getSpiderID($userAgent) {
		$spiderList = SpiderCacheBuilder::getInstance()->getData();
		$userAgent = strtolower($userAgent);
		
		foreach ($spiderList as $spider) {
			if (strpos($userAgent, $spider->spiderIdentifier) !== false) {
				return $spider->spiderID;
			}
		}
		
		return null;
	}
	
	protected function getMonths(){
		$months = array(
			WCF::getLanguage()->get('wcf.date.month.january'),
			WCF::getLanguage()->get('wcf.date.month.february'),
			WCF::getLanguage()->get('wcf.date.month.march'),
			WCF::getLanguage()->get('wcf.date.month.april'),
			WCF::getLanguage()->get('wcf.date.month.may'),
			WCF::getLanguage()->get('wcf.date.month.june'),
			WCF::getLanguage()->get('wcf.date.month.july'),
			WCF::getLanguage()->get('wcf.date.month.august'),
			WCF::getLanguage()->get('wcf.date.month.september'),
			WCF::getLanguage()->get('wcf.date.month.october'),
			WCF::getLanguage()->get('wcf.date.month.november'),
			WCF::getLanguage()->get('wcf.date.month.december')
		);
		return $months;
	}

}
