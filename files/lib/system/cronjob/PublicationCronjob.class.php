<?php
namespace cms\system\cronjob;

use cms\data\page\PageAction;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;

/**
 * Handles delayed publication and deactivation of pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PublicationCronjob extends AbstractCronjob {
	/**
	 * @see	\wcf\system\cronjob\ICronjob::execute()
	 */
	public function execute(Cronjob $cronjob) {
		// publish pages
		$sql = "SELECT	pageID
			FROM	cms".WCF_N."_page
			WHERE	isPublished = 0
				AND publicationDate <= ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(TIME_NOW));

		$pageIDs = array();
		while ($row = $statement->fetchArray()) {
			$pageIDs[] = $row['pageID'];
		}

		$action = new PageAction($pageIDs, 'publish');
		$action->executeAction();

		// disable pages
		$sql = "SELECT	pageID
			FROM	cms".WCF_N."_page
			WHERE	isDisabled = 0
				AND deactivationDate BETWEEN 1 AND ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(TIME_NOW));

		$pageIDs = array();
		while ($row = $statement->fetchArray()) {
			$pageIDs[] = $row['pageID'];
		}

		$action = new PageAction($pageIDs, 'disable');
		$action->executeAction();
	}
}
