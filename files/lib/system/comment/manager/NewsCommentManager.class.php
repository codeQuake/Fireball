<?php
namespace cms\system\comment\manager;

use cms\data\news\News;
use cms\data\news\NewsEditor;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCommentManager extends AbstractCommentManager {
	protected $permissionAdd = 'user.cms.news.canAddComment';
	protected $permissionCanModerate = 'mod.cms.news.canModerateComment';
	protected $permissionDelete = 'user.cms.news.canDeleteComment';
	protected $permissionEdit = 'user.cms.news.canEditComment';
	protected $permissionModDelete = 'mod.cms.news.canDeleteComment';
	protected $permissionModEdit = 'mod.cms.news.canEditComment';

	public function isAccessible($objectID, $validateWritePermission = false) {
		// check object id
		$news = new News($objectID);
		if (! $news->newsID || ! $news->canRead()) {
			return false;
		}
		
		return true;
	}

	public function getLink($objectTypeID, $objectID) {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'id' => $objectID
		));
	}

	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('cms.news.commentResponse');
		
		return WCF::getLanguage()->getDynamicVariable('cms.news.comment');
	}

	public function updateCounter($objectID, $value) {
		$news = new News($objectID);
		$editor = new NewsEditor($news);
		$editor->update(array(
			'comments' => ($news->comments + $value)
		));
	}
}
