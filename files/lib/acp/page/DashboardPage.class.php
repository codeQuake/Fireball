<?php
namespace cms\acp\page;

use cms\system\counter\VisitCountHandler;
use cms\data\news\NewsList;
use cms\data\page\PageList;
use wcf\data\user\online\UsersOnlineList;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

class DashboardPage extends AbstractPage {
	public $templateName = 'dashboard';
	public $activeMenuItem = 'cms.acp.menu.link.cms.page.dashboard';
	public $pages = null;
	public $news = null;
	public $usersOnlineList = null;

	protected function readFireballFeed() {
		$url = "http://codequake.de/index.php/NewsFeed/14/";
		try {
			$request = new HTTPRequest("http://codequake.de/index.php/NewsFeed/14/");
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
		}
		catch (\wcf\system\exception\SystemException $e) {
			return (array(
				'errorMessage' => $e->getMessage()
			));
		}
		
		if (! $xml = simplexml_load_string($feedData)) {
			die('Error reading feed!');
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

	public function readData() {
		parent::readData();
		// get pages
		$list = new PageList();
		$list->readObjects();
		$this->pages = $list->getObjects();
		
		// news
		$list = new NewsList();
		$list->readObjects();
		$this->news = $list->getObjects();
		
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

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'visitors' => VisitCountHandler::getInstance(),
			'feed' => $this->readFireballFeed(),
			'pages' => $this->pages,
			'news' => $this->news,
			'usersOnlineList' => $this->usersOnlineList,
			'server' => $this->server
		));
	}
}
