<?php
namespace cms\system\content\type;

use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\content\Content;
use cms\data\news\CategoryNewsList;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class NewsContentType extends AbstractContentType {

	protected $icon = 'icon-archive';

	public $objectType = 'de.codequake.cms.content.type.news';

	public function getFormTemplate() {
		$excludedCategoryIDs = array_diff(NewsCategory::getAccessibleCategoryIDs(), NewsCategory::getAccessibleCategoryIDs(array(
			'canAddNews'
		)));
		$categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news', 0, false, $excludedCategoryIDs);
		$categoryList = $categoryTree->getIterator();
		$categoryList->setMaxDepth(0);
		WCF::getTPL()->assign('categoryList', $categoryList);
		return 'newsContentType';
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$type = isset($data['type']) ? $data['type'] : 'standard';
		$list = new CategoryNewsList($data['categoryIDs']);
		$list->sqlLimit = CMS_NEWS_LATEST_LIMIT;
		$list->readObjects();
		$list = $list->getObjects();
		WCF::getTPL()->assign(array('objects' => $list,
									'type' => $type));
		return WCF::getTPL()->fetch('newsContentTypeOutput', 'cms');
	}
}
