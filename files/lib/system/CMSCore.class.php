<?php
namespace cms\system;

use cms\data\page\Page;
use cms\data\page\PageCache;
use cms\system\menu\page\CMSPageMenuItemProvider;
use wcf\system\application\AbstractApplication;
use wcf\system\menu\page\PageMenu;
use wcf\system\WCF;

/**
 * Fireball core.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSCore extends AbstractApplication {
	/**
	 * @see \wcf\system\application\AbstractApplication::$primaryController
	 */
	protected $primaryController = 'cms\page\PagePage';

	/**
	 * Finds and activates the related menu item for the given page. This
	 * function handles inherited menu items from parent pages and
	 * activates the most important menu item for the given page.
	 * Do notice that only header menu items are relevant for the search.
	 * 
	 * @param	\cms\data\page\Page	$page
	 */
	public static function setActiveMenuItem(Page $page) {
		$menuItemIDs = $menuItems = array();

		// 1) Create an array with all menu item ids of all parent
		//    pages. Menu items are sorted by their importance. The
		//    first entry is the most important, the last the least
		//    important.
		if ($page->menuItemID) {
			$menuItemIDs[] = $page->menuItemID;
		}
		while ($page->parentPageID && $page = PageCache::getInstance()->getPage($page->parentPageID)) {
			if ($page->menuItemID) {
				$menuItemIDs[] = $page->menuItemID;
			}
		}

		// 2) Search through all header menu items and check whether
		//    they are related to the page or its parent pages. Found
		//    menu items are again sorted by their importance.
		foreach (PageMenu::getInstance()->getMenuItems('header') as $menuItem) {
			if (($position = array_search($menuItem->menuItemID, $menuItemIDs)) !== false) {
				$menuItems[$position] = $menuItem->menuItem;
			}

			foreach (PageMenu::getInstance()->getMenuItems($menuItem->menuItem) as $subMenuItem) {
				if (($position = array_search($subMenuItem->menuItemID, $menuItemIDs)) !== false) {
					$menuItems[$position] = $subMenuItem->menuItem;
				}
			}
		}

		// 3) Active the most important menu item for this page.
		if (!empty($menuItems)) {
			$menuItem = array_shift($menuItems);
			PageMenu::getInstance()->setActiveMenuItem($menuItem);
		}
	}
}
