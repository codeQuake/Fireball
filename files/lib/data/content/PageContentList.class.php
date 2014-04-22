<?php
namespace cms\data\content;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class PageContentList extends ViewableContentList {
	public $pageID = 0;
	public $sqlOrderBy = 'content.showOrder ASC';

	public function __construct($pageID) {
		$this->pageID = $pageID;
		parent::__construct();
		
		$this->getConditionBuilder()->add('content.pageID = ?', array(
			$pageID
		));
	}
}
