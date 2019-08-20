<?php
namespace cms\system\cache\builder;

use cms\data\page\PageList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches general page information and the page structure.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	public function rebuild(array $parameters) {
		$data = [
			'aliasToPage' => [],
			'pages' => [],
			'stylesheetsToPage' => [],
			'structure' => [],
		];

		// fetch pages
		$pageList = new PageList();
		$pageList->sqlOrderBy = 'page.parentID ASC, page.showOrder ASC';
		$pageList->readObjects();

		$data['pages'] = $pageList->getObjects();

		foreach ($pageList as $page) {
			// Handle aliase to page assignment. Notice that we
			// can't simply use '$page->getAlias()' here since the
			// function would require a builded cache!
			$alias = $page->alias;
			$tmp = $page;
			while ($tmp->parentID && $tmp = $data['pages'][$tmp->parentID]) {
				$alias = $tmp->alias . '/' . $alias;
			}
			$data['aliasToPage'][$alias] = $page->pageID;

			// page structure
			$data['structure'][$page->parentID][] = $page->pageID;
			$data['wcfPageIDs'][$page->pageID] = $page->wcfPageID;
		}

		// stylesheets
		$sql = "SELECT	*
			FROM	cms".WCF_N."_stylesheet_to_page";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$data['stylesheetsToPage'][$row['pageID']][] = $row['stylesheetID'];
		}

		return $data;
	}
}
