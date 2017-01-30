<?php
namespace cms\util;

/**
 * Contains browser-related functions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
final class BrowserUtil {
	/**
	 * Returns the name of the browser with the given user agent.
	 * 
	 * @param	string		$userAgent
	 * @return	string
	 */
	public static function getBrowser($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		if (!$browser->isRobot()) return $browser->getBrowser();
		return 'unknown';
	}

	/**
	 * Returns the used platform.
	 * 
	 * @param	string		$userAgent
	 * @return	string
	 */
	public static function getPlatform($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->getPlatform();
	}

	/**
	 * Returns whether the given user agent is of a mobile browser.
	 * 
	 * @param	string		$userAgent
	 * @return	boolean
	 */
	public static function isMobile($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->isMobile();
	}

	/**
	 * Returns whether the given user agent is of a tablet device.
	 * 
	 * @param	string		$userAgent
	 * @return	boolean
	 */
	public static function isTablet($userAgent = '') {
		require_once(CMS_DIR.'lib/util/Browser.php');
		$browser = new \Browser($userAgent);
		return $browser->isTablet();
	}
}
