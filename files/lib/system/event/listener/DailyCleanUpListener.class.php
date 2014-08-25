<?php
namespace cms\system\event\listener;

use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DailyCleanUpListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// delete outdated revisions
		if (CMS_REVISION_DELETE) {
			// page revisions
			$sql = "DELETE FROM	cms" . WCF_N . "_page_revision
				WHERE		time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				TIME_NOW - (CMS_REVISION_DELETE * 86400)
			));
			
			// content revisions
			$sql = "DELETE FROM	cms" . WCF_N . "_content_revision
				WHERE		time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				TIME_NOW - (CMS_REVISION_DELETE * 86400)
			));
		}
	}
}
