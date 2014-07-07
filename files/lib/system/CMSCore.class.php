<?php
namespace cms\system;

use cms\data\page\Page;
use cms\data\page\PageCache;
use cms\system\menu\page\CMSPageMenuItemProvider;
use wcf\system\application\AbstractApplication;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\menu\page\PageMenu;
use wcf\system\WCF;

/**
 * Fireball core.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSCore extends AbstractApplication {

	/**
	 *
	 * @see AbstractApplication::$abbreviation
	 */
	protected $abbreviation = 'cms';

	/**
	 *
	 * @see \wcf\system\application\AbstractApplication::$primaryController
	 */
	protected $primaryController = 'cms\page\PagePage';

	public static function setActiveMenuItem(Page $page) {
		if ($page->menuItemID) {
			$menuItemID = $page->menuItemID;
		} else if (PageCache::getInstance()->getHomePage() !== null) $menuItemID = PageCache::getInstance()->getHomePage()->menuItemID;
		else $menuItemID = PageMenu::getInstance()->getLandingPage()->menuItemID;
		
		foreach (PageMenu::getInstance()->getMenuItems('header') as $item) {
			if ($item->menuItemID == $menuItemID) PageMenu::getInstance()->setActiveMenuItem($item->menuItem);
		}
	}

	public static function setBreadcrumbs(Page $page) {
		if (PageMenu::getInstance()->getLandingPage()->getProcessor() instanceof CMSPageMenuItemProvider) {
			$pageID = PageMenu::getInstance()->getLandingPage()
				->getProcessor()
				->getPage()->pageID;
		}
		if (isset($pageID) && $pageID == $page->pageID) {
			WCF::getBreadcrumbs()->remove(0);
		}
		
		// add breadcrumbs
		foreach ($page->getParentPages() as $child) {
			WCF::getBreadcrumbs()->add(new Breadcrumb($child->getTitle(), $child->getLink()));
		}
	}
}
