<?php
namespace cms\system\dashboard\box;

use cms\data\page\PageList;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Dashboard sidebar box for most recently created pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LatestPagesDashboardBox extends AbstractSidebarDashboardBox {
	/**
	 * list of latest pages
	 * @var	\cms\data\page\PageList
	 */
	public $pageList = null;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$this->pageList = new PageList();
		$this->pageList->sqlLimit = FIREBALL_DASHBOARD_SIDEBAR_ENTRIES;
		$this->pageList->sqlOrderBy = 'page.creationTime DESC';
		$this->pageList->readObjects();

		$this->fetched();
	}

	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->pageList)) return '';

		WCF::getTPL()->assign(array(
			'pageList' => $this->pageList
		));

		return WCF::getTPL()->fetch('dashboardBoxLatestPages', 'cms');
	}
}
