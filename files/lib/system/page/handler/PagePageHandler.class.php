<?php

namespace cms\system\page\handler;
use cms\data\page\PageCache;
use wcf\system\page\handler\AbstractMenuPageHandler;

class PagePageHandler extends AbstractMenuPageHandler {
	public function isVisible($objectID = null) {
		$page = PageCache::getInstance()->getPage($objectID);

		if ($page === null)
			return false;

		return !$page->invisible && $page->canRead();
	}
}
