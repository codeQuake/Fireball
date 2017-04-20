<?php

namespace cms\system\page\handler;

use cms\data\page\PageCache;
use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;
use wcf\system\page\handler\TOnlineLocationPageHandler;
use wcf\system\WCF;

trait TPageOnlineLocationPageHandler {
	use TOnlineLocationPageHandler;
	
	/**
	 * @inheritdoc
	 */
	public function getOnlineLocation(Page $page, UserOnline $user) {
		$pageID = $user->pageID;
		$objectID = $user->pageObjectID;
		$fPage = null;
		
		if ($objectID) {
			$fPage = PageCache::getInstance()->getPage($user->pageObjectID);
		}
		else {
			if ($pageID) {
				$pages = PageCache::getInstance()->getPages();
				foreach ($pages as $item) {
					if ($item->wcfPageID == $pageID) {
						$fPage = $item;
						break;
					}
				}
			}
		}
		
		if ($fPage === null || !$fPage->canRead()) {
			return '';
		}
		
		return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.de.codequake.cms.Page', ['page' => $fPage]);
	}
}
