<?php

namespace cms\system\page\handler;
use cms\data\page\Page;
use cms\data\page\PageCache;
use wcf\data\user\online\UserOnline;
use wcf\system\page\handler\TOnlineLocationPageHandler;
use wcf\system\WCF;

trait TPageOnlineLocationPageHandler {
	use TOnlineLocationPageHandler;

	/**
	 * @inheritdoc
	 */
	public function getOnlineLocation(Page $page, UserOnline $user) {
		if ($user->pageObjectID === null)
			return '';

		$page = PageCache::getInstance()->getPage($user->pageObjectID);
		if ($page === null || $page->canRead())
			return '';
		
		return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, array('page' => $page));
	}
}
