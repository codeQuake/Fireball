<?php
namespace cms\system\menu\page;

use cms\data\page\PageCache;
use wcf\system\menu\page\DefaultPageMenuItemProvider;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class CMSPageMenuItemProvider extends DefaultPageMenuItemProvider {
	protected $page = null;

	public function getPage() {
		$tmp = explode("=", $this->getDecoratedObject()->menuItemLink);
		$this->page = PageCache::getInstance()->getPage(intval($tmp[1]));
	}

	public function getLink() {
		$this->getPage();
		if ($this->page === null || $this->page->pageID == 0) return parent::getLink();
		return $this->page->getLink();
	}
}
