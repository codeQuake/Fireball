<?php
namespace cms\system\cache\builder;

use cms\data\page\Page;
use cms\data\page\PageList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCacheBuilder extends AbstractCacheBuilder {

	public function rebuild(array $parameters) {
		$data = array(
			'pages' => array(),
			'aliasToID' => array(),
			'tree' => array()
		);
		
		$list = new PageList();
		$list->sqlOrderBy = 'parentID ASC, showOrder ASC';
		$list->readObjects();
		$data['pages'] = $list->getObjects();
		foreach ($data['pages'] as $page) {
			
			$alias = $page->alias;
			$tmp = $page;
			while ($tmp->parentID && $tmp = $data['pages'][$tmp->parentID]) {
				$alias = $tmp->alias . '/' . $alias;
			}
			
			$data['aliasToID'][$alias] = $page->pageID;
			
			$data['tree'][$page->parentID][] = $page->pageID;
		}
		return $data;
	}
}
