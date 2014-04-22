<?php
namespace cms\acp\page;

use wcf\page\SortablePage;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms.news.rss
 */
class FeedListPage extends SortablePage {
	public $objectListClassName = 'cms\data\feed\FeedList';
	public $activeMenuItem = 'cms.acp.menu.link.cms.feed.list';
	public $neededPermissions = array(
		'admin.cms.news.canAddFeed'
	);
	public $templateName = 'feedList';
	public $defaultSortfield = 'title';
	public $validSortFields = array(
		'feedID',
		'title'
	);
}
