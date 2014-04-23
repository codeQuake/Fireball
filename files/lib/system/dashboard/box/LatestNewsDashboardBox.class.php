<?php
namespace cms\system\dashboard\box;

use cms\data\news\LatestNewsList;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LatestNewsDashboardBox extends AbstractSidebarDashboardBox {
	public $latestNews = null;

	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$this->latestNews = new LatestNewsList();
		$this->latestNews->sqlLimit = CMS_NEWS_LATEST_LIMIT;
		$this->latestNews->readObjects();
	}

	protected function render() {
		if (! count($this->latestNews)) return '';
		
		WCF::getTPL()->assign(array(
			'latestNews' => $this->latestNews
		));
		
		return WCF::getTPL()->fetch('dashboardBoxLatestNews', 'cms');
	}
}
