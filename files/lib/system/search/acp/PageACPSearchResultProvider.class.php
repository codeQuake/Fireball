<?php
namespace cms\system\search\acp;

use cms\system\cache\builder\PageCacheBuilder;
use wcf\system\request\LinkHandler;
use wcf\system\search\acp\ACPSearchResult;
use wcf\system\search\acp\IACPSearchResultProvider;
use wcf\system\WCF;

/**
 * ACP search result provider implementation for cms pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageACPSearchResultProvider implements IACPSearchResultProvider {
	/**
	 * @see	\wcf\system\search\acp\IACPSearchResultProvider::search()
	 */
	public function search($query) {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.page.canAddPage')) {
			return [];
		}

		$results = [];

		$pages = PageCacheBuilder::getInstance()->getData([], 'pages');
		foreach ($pages as $page) {
			if (mb_stripos($page->getTitle(), $query) !== false || mb_stripos($page->alias, $query) !== false) {
				$link = LinkHandler::getInstance()->getLink('PageEdit', [
					'application' => 'cms',
					'id' => $page->pageID
				]);

				$subtitle = WCF::getLanguage()->getDynamicVariable('cms.page.parents', [
					'page' => $page
				]);

				$results[] = new ACPSearchResult($page->getTitle(), $link, $subtitle);
			}
		}

		return $results;
	}
}
