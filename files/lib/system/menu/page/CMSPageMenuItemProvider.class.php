<?php
namespace cms\system\menu\page;

use cms\data\page\PageCache;
use wcf\system\menu\page\DefaultPageMenuItemProvider;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSPageMenuItemProvider extends DefaultPageMenuItemProvider {

	protected $page = null;

	public function getPage() {
		if ($this->page === null) {
			$matches = array();
			preg_match('/id=(\d+)/', $this->menuItemLink, $matches);

			if (isset($matches[1])) $this->page = PageCache::getInstance()->getPage($matches[1]);
			else $this->page = PageCache::getInstance()->getHomePage();
		}

		return $this->page;
	}

	public function getLink() {
		;
		if ($this->getPage() === null) return parent::getLink();
		return $this->getPage()->getLink();
	}
}
