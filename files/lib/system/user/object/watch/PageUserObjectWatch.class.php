<?php
namespace cms\system\user\object\watch;

use cms\data\page\PageCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\object\watch\IUserObjectWatch;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Implementation of IUserObjectWatch for watched pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageUserObjectWatch implements IUserObjectWatch {
	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::validateObjectID()
	 */
	public function validateObjectID($objectID) {
		$page = PageCache::getInstance()->getPage($objectID);
		if ($page === null) {
			throw new IllegalLinkException();
		}

		// check permission
		if (!$page->isAccessible()) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see	\wcf\system\user\object\watch\IUserObjectWatch::resetUserStorage()
	 */
	public function resetUserStorage(array $userIDs) {
		UserStorageHandler::getInstance()->reset($userIDs, 'cmsUnreadWatchedPages');
	}
}
