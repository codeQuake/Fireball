<?php
namespace cms\acp\page;

use cms\system\counter\VisitCountHandler;
use cms\data\news\NewsList;
use cms\data\page\PageList;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\AbstractPage;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

/**
 * Shows the dashboard.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DashboardPage extends AbstractPage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.dashboard';

	public $pages = null;

	public $usersOnlineList = null;

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// get pages
		$list = new PageList();
		$list->readObjects();
		$this->pages = $list->getObjects();

		// onlinelist
		$this->usersOnlineList = new UsersOnlineList();
		$this->usersOnlineList->readStats();
		$this->usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
		$this->usersOnlineList->readObjects();

		// system info
		$this->server = array(
			'os' => PHP_OS,
			'webserver' => (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : ''),
			'mySQLVersion' => WCF::getDB()->getVersion(),
			'load' => ''
		);
	}

	protected function readFireballFeed() {
		$url = "http://codequake.de/index.php/NewsFeed/26/";
		try {
			$request = new HTTPRequest($url);
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
		}
		catch (SystemException $e) {
			return (array(
				'errorMessage' => $e->getMessage()
			));
		}

		if (! $xml = simplexml_load_string($feedData)) {
			return array();
		}
		$feed = array();
		$i = 2;

		foreach ($xml->channel[0]->item as $item) {
			if ($i -- == 0) {
				break;
			}

			$feed[] = array(
				'title' => (string) $item->title,
				'description' => (string) $item->description,
				'link' => (string) $item->guid,
				'date' => date('d.m.Y H:i', strtotime((string) $item->pubDate))
			);
		}
		return $feed;
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'visitors' => VisitCountHandler::getInstance(),
			'feed' => $this->readFireballFeed(),
			'pages' => $this->pages,
			'usersOnlineList' => $this->usersOnlineList,
			'server' => $this->server
		));
	}
}
