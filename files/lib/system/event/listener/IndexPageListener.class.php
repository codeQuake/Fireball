<?php
namespace cms\system\event\listener;

use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

/**
 * Fetches the codeQuake RSS feed to show news on the acp overciew page.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class IndexPageListener implements IParameterizedEventListener {
	const FEED_URL = 'http://codequake.de/index.php/NewsFeed/';

	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		try {
			$request = new HTTPRequest(self::FEED_URL);
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
			
			if (empty($feedData) || !$xml = @simplexml_load_string($feedData)) {
				return;
			}
		}
		catch (SystemException $e) {
			// log error
			$e->getExceptionID();

			return;
		}

		$feed = array();
		$i = 10;

		foreach ($xml->channel[0]->item as $item) {
			if ($i -- == 0) {
				break;
			}

			$feed[] = array(
				'title' => (string) $item->title,
				'description' => (string) $item->description,
				'link' => (string) $item->guid,
				'time' => strtotime((string) $item->pubDate)
			);
		}

		WCF::getTPL()->assign(array(
			'codequakeNewsFeed' => $feed
		));
	}
}
