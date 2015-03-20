<?php
namespace cms\system\event\listener;

use cms\data\file\FileAction;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Extends the daily system cleanup.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DailyCleanUpListener implements IParameterizedEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// delete obsolete file uploads
		// files are considered obsolete when they are not assigned to
		// at least one category and are older than one day
		$sql = "SELECT		file.fileID
			FROM		cms".WCF_N."_file file
			LEFT JOIN	cms".WCF_N."_file_to_category file_to_category ON (file.fileID = file_to_category.fileID)
			WHERE		file_to_category.categoryID IS NULL
					AND	file.uploadTime < ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(TIME_NOW - 86400));

		$fileIDs = array();
		while ($row = $statement->fetchArray()) {
			$fileIDs[] = $row['fileID'];
		}

		$fileAction = new FileAction($fileIDs, 'delete');
		$fileAction->executeAction();

		// delete outdated revisions
		if (CMS_REVISION_DELETE) {
			$sql = "DELETE FROM	cms".WCF_N."_page_revision
				WHERE		time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(TIME_NOW - (CMS_REVISION_DELETE * 86400)));
		}

		// delete old statistics
		if (CMS_PAGES_STATISTICS_DELETE) {
			$sql = "DELETE FROM	cms".WCF_N."_counter
				WHERE		UNIX_TIMESTAMP(DATE_ADD(DATE_ADD(MAKEDATE(year, 1), INTERVAL (month)-1 MONTH), INTERVAL (day)-1 DAY)) < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(TIME_NOW - (CMS_PAGES_STATISTICS_DELETE * 86400)));
		}
	}
}
