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
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) {
			return array();
		}

		$results = array();

		$pages = PageCacheBuilder::getInstance()->getData(array(), 'pages');
		foreach ($pages as $page) {
			if (mb_strpos($page->getTitle(), $query) !== -1 || mb_strpos($page->alias, $query) !== -1) {
				$link = LinkHandler::getInstance()->getLink('PageEdit', array(
					'application' => 'cms',
					'id' => $page->pageID
				));

				$subtitle = WCF::getLanguage()->getDynamicVariable('cms.page.parents', array(
					'parentPages' => $page->getParents()
				));

				$results[] = new ACPSearchResult($page->getTitle(), $link, $subtitle);
			}
		}

		return $results;
	}
}
