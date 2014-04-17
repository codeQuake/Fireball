<?php
namespace cms\system\comment\manager;
use cms\data\page\Page;
use cms\data\page\PageEditor;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class PageCommentManager extends AbstractCommentManager{

    protected $permissionAdd = 'user.cms.page.canAddComment';
    protected $permissionCanModerate = 'mod.cms.page.canModerateComment';
    protected $permissionDelete = 'user.cms.page.canDeleteComment';
    protected $permissionEdit = 'user.cms.page.canEditComment';
    protected $permissionModDelete = 'mod.cms.page.canDeleteComment';
    protected $permissionModEdit = 'mod.cms.page.canEditComment';
    
    public function isAccessible($objectID, $validateWritePermission = false) {
		// check object id
		$page = new Page($objectID);
		if (!$page->pageID || !$page->isVisible()) {
			return false;
		}
		
		return true;
	}
    
    public function getLink($objectTypeID, $objectID) {
		return LinkHandler::getInstance()->getLink('Page', array(
			'application' => 'cms',
			'id' => $objectID
		));
	}
    
    public function getTitle($objectTypeID, $objectID, $isResponse = false) {
		if ($isResponse) return WCF::getLanguage()->get('cms.page.commentResponse');
		
		return WCF::getLanguage()->getDynamicVariable('cms.page.comment');
	}
    
    public function updateCounter($objectID, $value) {
		$page = new Page($objectID);
		$editor = new PageEditor($page);
		$editor->update(array(
			'comments' => ($page->comments + $value)
		));
	}
}
