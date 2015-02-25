<?php

namespace cms\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Updates usernames when username has changed
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class UserRenameListener implements IParameterizedEventListener {
	/**
	 * @see \wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		$objects = $eventObj->getObjects();
		$userID = $objects[0]->userID;
		$parameters = $eventObj->getParameters();
		$username = $parameters['data']['username'];
		
		$sql = array();
		// content versions
		$sql[] = "SELECT		cms" . WCF_N . "_content_version
			SET		username = ?
			WHERE		userID = ?";
		// news
		$sql[] = "SELECT		cms" . WCF_N . "_news
			SET		username = ?
			WHERE		userID = ?";
		// nwes updates
		$sql[] = "SELECT		cms" . WCF_N . "_news_update
			SET		username = ?
			WHERE		userID = ?";
		// pages
		$sql[] = "SELECT		cms" . WCF_N . "_page
			SET		authorName = ?
			WHERE		authorID = ?";
		// page revisions
		$sql[] = "SELECT		cms" . WCF_N . "_page_revision
			SET		username = ?
			WHERE		userID = ?";
		
		foreach ($sql as $query) {
			$statement = WCF::getDB()->prepareStatement($query);
			$statement->execute(array($username, $userID));
		}
	}
}
