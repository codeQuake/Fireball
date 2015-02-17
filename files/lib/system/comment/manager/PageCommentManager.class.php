<?php
namespace cms\system\comment\manager;

use cms\data\page\PageCache;
use cms\data\page\PageEditor;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Page comment manager.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentManager extends AbstractCommentManager {
	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionAdd
	 */
	protected $permissionAdd = 'user.cms.page.canAddComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionCanModerate
	 */
	protected $permissionCanModerate = 'mod.cms.page.canModerateComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionDelete
	 */
	protected $permissionDelete = 'user.cms.page.canDeleteComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionEdit
	 */
	protected $permissionEdit = 'user.cms.page.canEditComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionModDelete
	 */
	protected $permissionModDelete = 'mod.cms.page.canDeleteComment';

	/**
	 * @see	\wcf\system\comment\manager\AbstractCommentManager::$permissionModEdit
	 */
	protected $permissionModEdit = 'mod.cms.page.canEditComment';

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::canAdd()
	 */
	public function canAdd($objectID) {
		if (parent::canAdd($objectID)) {
			$page = PageCache::getInstance()->getPage($objectID);
			return $page->getPermission('canAddComment');
		}

		return false;
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getLink()
	 */
	public function getLink($objectTypeID, $objectID) {
		$page = PageCache::getInstance()->getPage($objectID);

		return $page->getLink();
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::getTitle()
	 */
	public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('cms.page.commentResponse');
		
		return WCF::getLanguage()->getDynamicVariable('cms.page.comment');
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::isAccessible()
	 */
	public function isAccessible($objectID, $validateWritePermission = false) {
		$page = PageCache::getInstance()->getPage($objectID);

		return ($page !== null && $page->canRead());
	}

	/**
	 * @see	\wcf\system\comment\manager\ICommentManager::updateCounter()
	 */
	public function updateCounter($objectID, $value) {
		$page = PageCache::getInstance()->getPage($objectID);
		$editor = new PageEditor($page);
		$editor->updateCounters(array(
			'comments' => $value
		));
	}
}
