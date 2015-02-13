<?php
namespace cms\system\menu\page;

use cms\data\page\PageCache;
use wcf\system\menu\page\DefaultPageMenuItemProvider;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSPageMenuItemProvider extends DefaultPageMenuItemProvider {
	/**
	 * page object
	 * @var	\cms\data\page\Page
	 */
	protected $page = null;

	/**
	 * @see	\wcf\system\menu\page\IPageMenuItemProvider::isVisible()
	 */
	public function isVisible() {
		if ($this->page === null) $this->getPage();

		return ($this->page !== null && $this->page->isVisible());
	}

	/**
	 * Returns the page this menu item links to.
	 * 
	 * @var	\cms\data\page\Page
	 */
	public function getPage() {
		if ($this->page === null) {
			$matches = array();
			preg_match('/id=(\d+)/', $this->menuItemLink, $matches);

			if (isset($matches[1])) $this->page = PageCache::getInstance()->getPage($matches[1]);
			else $this->page = PageCache::getInstance()->getHomePage();
		}

		return $this->page;
	}

	/**
	 * @see	\wcf\system\menu\page\IPageMenuItemProvider::getLink()
	 */
	public function getLink() {
		if ($this->getPage() === null) return parent::getLink();
		return $this->getPage()->getLink();
	}
}
