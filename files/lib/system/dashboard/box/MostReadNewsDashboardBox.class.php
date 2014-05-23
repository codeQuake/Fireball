<?php
namespace cms\system\dashboard\box;

use cms\data\news\MostReadNewsList;
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
class MostReadNewsDashboardBox extends AbstractSidebarDashboardBox {
	public $mostReadNews = null;

	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$this->mostReadNews = new MostReadNewsList();
		$this->mostReadNews->sqlLimit = CMS_NEWS_LATEST_LIMIT;
		$this->mostReadNews->readObjects();
	}

	protected function render() {
		if (! count($this->mostReadNews)) return '';

		WCF::getTPL()->assign(array(
		'mostReadNews' => $this->mostReadNews
		));

		return WCF::getTPL()->fetch('dashboardBoxMostReadNews', 'cms');
	}
}
