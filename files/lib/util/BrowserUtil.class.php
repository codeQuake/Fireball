<?php
namespace cms\util;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

final class BrowserUtil {

	public static function getBrowser($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		if (!$browser->isRobot()) return $browser->getBrowser();
		return 'Unknown';
	}

	public static function getPlatform($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->getPlatform();
	}

	public static function isMobile($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->isMobile();
	}

	public static function isTablet($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->isTablet();
	}
}
