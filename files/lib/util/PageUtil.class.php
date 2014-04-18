<?php
namespace cms\util;
use cms\data\page\PageCache;

/**
 * Contains page-related functions.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
final class PageUtil {
	const ALIAS_PATTERN = '[a-z0-9]+(?:\-{1}[a-z0-9]+)*';

	/**
	 * Returns true if the given alias is available at the given position.
	 * With the optional third parameter, you can specifiy a page that is
	 * excluded from checking agains. For example, you can use that to
	 * exclude the page the new alias will be assigned to.
	 * 
	 * @param	string		$alias
	 * @param	integer		$parentPageID
	 * @param	integer		$excludedPageID
	 * @return	boolean
	 */
	public static function isAvailableAlias($alias, $parentPageID, $excludedPageID = null) {
		$childIDs = PageCache::getInstance()->getChildIDs($parentPageID);
		die(var_dump($childIDs));

		if (!empty($childIDs)) {
			foreach ($childIDs as $childID) {
				if ($childID == $excludedPageID) continue;

				$page = PageCache::getInstance()->getPage($childID);
				if ($page->alias == $alias) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns true if the given alias matches the general alias pattern.
	 * 
	 * @param	string		$alias
	 * @return	boolean
	 */
	public static function isValidAlias($alias) {
		return preg_match('~^'. self::ALIAS_PATTERN .'$~', $alias);
	}

	private function __construct() { /* nothing */ }
}
