<?php
namespace cms\system\comment\manager;

use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\WCF;

/**
 * Page comment manager.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentManager extends AbstractCommentManager {
	/**
	 * @inheritDoc
	 */
	protected $permissionAdd = 'user.fireball.page.canAddComment';

	/**
	 * @inheritDoc
	 */
	protected $permissionCanModerate = 'mod.fireball.canModerateComment';

	/**
	 * @inheritDoc
	 */
	protected $permissionDelete = 'user.fireball.canDeleteComment';

	/**
	 * @inheritDoc
	 */
	protected $permissionEdit = 'user.fireball.canEditComment';

	/**
	 * @inheritDoc
	 */
	protected $permissionModDelete = 'mod.fireball.canDeleteComment';

	/**
	 * @inheritDoc
	 */
	protected $permissionModEdit = 'mod.fireball.canEditComment';

	/**
	 * @inheritDoc
	 */
	public function canAdd($objectID) {
		if (parent::canAdd($objectID)) {
			$page = PageCache::getInstance()->getPage($objectID);
			return $page->getPermission('user.canAddComment');
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getLink($objectTypeID, $objectID) {
		$page = PageCache::getInstance()->getPage($objectID);

		return $page->getLink();
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) {
			return WCF::getLanguage()->get('cms.page.commentResponse');
		}

		return WCF::getLanguage()->getDynamicVariable('cms.page.comment');
	}

	/**
	 * @inheritDoc
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		$page = PageCache::getInstance()->getPage($objectID);
		return ($page !== null && $page->canRead());
	}

	/**
	 * @inheritDoc
	 */
	public function updateCounter($objectID, $value) {
		$page = PageCache::getInstance()->getPage($objectID);
		$editor = new PageEditor($page);
		$editor->updateCounters([
			'comments' => $value
		]);
	}
}
