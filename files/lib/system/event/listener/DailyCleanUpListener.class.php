<?php
namespace cms\system\event\listener;

use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */

class DailyCleanUpListener implements IEventListener {

	public function execute($eventObj, $className, $eventName) {
		//delete outdated revisions
		if (CMS_REVISION_DELETE) {
			//page revisions
			$sql = "DELETE FROM	cms" . WCF_N . "_page_revision
				WHERE		time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				TIME_NOW - (CMS_REVISION_DELETE * 86400)
			));
			
			//content revisions
			$sql = "DELETE FROM	cms" . WCF_N . "_content_revision
				WHERE		time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				TIME_NOW - (CMS_REVISION_DELETE * 86400)
			));
		}
	}
}
