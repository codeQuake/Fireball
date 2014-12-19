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
	/**
	 * maximum length of an alias
	 * @var	integer
	 */
	const ALIAS_MAXLENGTH = 25;

	/**
	 * minimum length of an alias
	 * @var	integer
	 */
	const ALIAS_MINLENGTH = 3;

	/**
	 * general pattern for an alias
	 * @var	string
	 */
	const ALIAS_PATTERN = '[a-z0-9]+(?:\-{1}[a-z0-9]+)*';

	/**
	 * pattern of a stack of aliases, devided by slashes
	 * @var	string
	 */
	const ALIAS_PATTERN_STACK = '[a-z0-9]+(?:\-{1}[a-z0-9]+)*(?:\/[a-z0-9]+(?:\-{1}[a-z0-9]+)*)*';

	/**
	 * Builds an alias based upon the given page title automatically.
	 * Caution: Though the generated alias is valid, you have to check
	 * whether the alias is available at the used position.
	 * 
	 * @param	string		$title
	 * @return	string
	 */
	public static function buildAlias($title) {
		// make alias lowercase
		$alias = mb_strtolower($title);

		// replace whitespace with hyphen
		$alias = str_replace(' ', '-', $alias);

		// remove illegal characters
		$alias = preg_replace('~[^a-z0-9\-]+~', '', $alias);

		// trim to maxlength
		$alias = mb_substr($alias, 0, self::ALIAS_MAXLENGTH);

		// remove hyphens from start and end of alias
		$alias = trim($alias, '-');

		return $alias;
	}

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
		// check for min and maxlength
		if (mb_strlen($alias) < self::ALIAS_MINLENGTH || mb_strlen($alias) > self::ALIAS_MAXLENGTH) {
			return false;
		}

		return (preg_match('~^' . self::ALIAS_PATTERN . '$~', $alias) == 1);
	}

	private function __construct() { /* nothing */ }
}
