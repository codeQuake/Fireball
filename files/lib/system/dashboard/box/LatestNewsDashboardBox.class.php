<?php
namespace cms\system\dashboard\box;
use cms\data\news\LatestNewsList;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

class LatestNewsDashboardBox extends AbstractSidebarDashboardBox {
    public $latestNews = null;
    
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$this->latestNews = new LatestNewsList();
        $this->latestNews->sqlLimit = CMS_NEWS_LATEST_LIMIT;
		$this->latestNews->readObjects();
	}

	protected function render() {
		if (!count($this->latestNews)) return '';
		
		WCF::getTPL()->assign(array(
			'latestNews' => $this->latestNews
		));
		
		return WCF::getTPL()->fetch('dashboardBoxLatestNews', 'cms');
	}
}
