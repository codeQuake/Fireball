<?php
namespace cms\system\sitemap;

use cms\data\category\NewsCategoryNodeTree;
use wcf\system\sitemap\ISitemapProvider;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategorySitemapProvider implements ISitemapProvider {
	public $objectTypeName = 'de.codequake.cms.category.news';

	public function getTemplate() {
		if (MODULE_NEWS) {
			$nodeTree = new NewsCategoryNodeTree($this->objectTypeName);
			$nodeList = $nodeTree->getIterator();
			
			WCF::getTPL()->assign(array(
				'nodeList' => $nodeList
			));
			
			return WCF::getTPL()->fetch('newsSitemap', 'cms');
		}
		return;
	}
}
