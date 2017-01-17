<?php
namespace cms\system\counter;

use cms\util\BrowserUtil;
use wcf\system\cache\builder\SpiderCacheBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class VisitCountHandler extends SingletonFactory {
	protected function canCount() {
		if (WCF::getSession()->getVar('counted')) return false;
		return true;
	}

	public function count() {
		if (!FIREBALL_PAGES_ENABLE_STATISTICS) {
			return;
		}

		if ($this->canCount()) {
			$userID = WCF::getUser()->userID;
			$spider = $this->getSpiderID(WCF::getSession()->userAgent);
			if ($spider === null) $spider = 0;
			$browser = BrowserUtil::getBrowser(WCF::getSession()->userAgent);
			$platform = BrowserUtil::getPlatform(WCF::getSession()->userAgent);
			$isTablet = BrowserUtil::isTablet(WCF::getSession()->userAgent);
			$isMobile = BrowserUtil::isMobile(WCF::getSession()->userAgent);

			// update
			if ($this->existingColumn()) {
				$sql = "SELECT	*
					FROM	cms" . WCF_N . "_counter
					WHERE	day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . "
						AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . "
						AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute();
				$counter = $statement->fetchArray();

				$browsers = @unserialize($counter['browsers']);
				$platforms = @unserialize($counter['platforms']);
				$devices = @unserialize($counter['devices']);
				// save browser
				if (isset($browsers[$browser])) $browsers[$browser] = $browsers[$browser] + 1;
				else $browsers[$browser] = 1;
				// save platform
				if (isset($platforms[$platform])) $platforms[$platform] = $platforms[$platform] + 1;
				else $platforms[$platform] = 1;
				// save device
				if ($isMobile) $device = 'mobile';
				else if ($isTablet) $device = 'tablet';
				else $device = 'desktop';
				if (isset($devices[$device])) $devices[$device] = $devices[$device] + 1;
				else $devices[$device] = 1;
				// save visits
				$users = $counter['users'];
				if ($userID != 0) $users ++;
				$spiders = $counter['spiders'];
				if ($spider != 0) $spiders ++;
				$visits = $counter['visits'] + 1;

				$sql = "UPDATE	cms" . WCF_N . "_counter
					SET	visits = ?,
						users = ?,
						spiders = ?,
						browsers = ?,
						platforms = ?,
						devices = ?
					WHERE	day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . "
						AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . "
						AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute([
					$visits,
					$users,
					$spiders,
					serialize($browsers),
					serialize($platforms),
					serialize($devices)
				]);
			}

			// create new
			else {
				$users = 0;
				$spiders = 0;
				if ($userID != 0) $users ++;
				if ($spider != 0) $spiders ++;
				$browsers = [];
				$browsers[$browser] = 1;
				$platforms = [];
				$platforms[$platform] = 1;

				if ($isMobile) $device = 'mobile';
				else if ($isTablet) $device = 'tablet';
				else $device = 'desktop';

				$devices = [];
				$devices[$device] = 1;

				$sql = "INSERT INTO	cms" . WCF_N . "_counter
							(day, month, year, visits, users, spiders, browsers, platforms, devices)
					VALUES		(?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute([
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j'),
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n'),
					DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y'),
					1,
					$users,
					$spiders,
					serialize($browsers),
					serialize($platforms),
					serialize($devices)
				]);
			}
			WCF::getSession()->register('counted', true);
		}
	}

	public function existingColumn() {
		$sql = "SELECT	COUNT(*) AS amount
			FROM	cms" . WCF_N . "_counter
			WHERE	day = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j') . "
				AND month = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n') . "
				AND year = " . DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();

		if ($statement->fetchColumn() != 0) return true;
		return false;
	}

	public function getVisitors($start, $end) {
		$vistors = [];
		$date = $start;
		while ($date <= $end) {
			$months = $this->getMonths();
			$visitors[] = [
				'visitors' => $this->getDailyVisitors(DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'j'), DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'n'), DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'Y')),
				'string' => DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'j') . '. ' . $months[DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'n') - 1] . ' ' . DateUtil::format(DateUtil::getDateTimeByTimestamp($date), 'Y')
			];
			$date = $date + 86400;
		}
		return $visitors;
	}

	public function getAllVisitors() {
		$sql = "SELECT	*
			FROM	cms" . WCF_N . "_counter";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$count = 0;
		while ($row = $statement->fetchArray()) {
			$count = $count + $row['visits'];
		}
		return $count;
	}

	public function getDailyVisitors($day = 10, $month = 2, $year = 2014) {
		$sql = "SELECT	*
			FROM	cms" . WCF_N . "_counter
			WHERE	day = ?
				AND month = ?
				AND year = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			$day,
			$month,
			$year
		]);
		return $statement->fetchArray();
	}

	public function getWeeklyVisitorArray() {
		$currentMonth = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'n');
		$currentYear = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'Y');
		$currentDay = DateUtil::format(DateUtil::getDateTimeByTimestamp(TIME_NOW), 'j');

		$visitors = [];
		$year = $currentYear;
		$month = $currentMonth;
		$day = $currentDay;

		for ($i = 1; $i <= 7; $i ++) {
			$months = $this->getMonths();
			$visitors[$i] = [
				'string' => $day . '. ' . $months[$month - 1] . ' ' . $year,
				'visitors' => $this->getDailyVisitors($day, $month, $year)
			];
			$day --;
			if ($day == 0) {
				$month --;
				if (in_array($month, [
					1,
					3,
					5,
					7,
					8,
					10,
					12
				])) $day = 31;
				if ($month == 2) $day = 28;
				else $day = 30;
			}
			if ($month == 0) {
				$month = 12;
				$year = $currentYear - 1;
			}
		}
		return array_reverse($visitors);
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

	protected function getMonths() {
		$months = [
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
		];
		return $months;
	}
}
