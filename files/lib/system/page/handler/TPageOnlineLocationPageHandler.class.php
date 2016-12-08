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
		if ($user->pageObjectID === null)
			return '';

		$fPage = PageCache::getInstance()->getPage($user->pageObjectID);
		if ($fPage === null || !$fPage->canRead())
			return '';

		return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, array('page' => $fPage));
	}
}
