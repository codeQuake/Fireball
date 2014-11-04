<?php
namespace cms\system\content;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\request\RequestHandler;

/**
 * Dashboard content implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DashboardContent extends AbstractContent {
	/**
	 * dashboard box object
	 * @var	\cms\system\page\element\IDashboardBox
	 */
	public $dashboardBox = null;

	/**
	 * @see	\cms\system\content\IContent::readData()
	 */
	public function readData() {
		parent::readData();

		$boxes = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');

		// init box
		$this->dashboardBox = new $boxes[$this->boxID]->className;
		$this->dashboardBox->init($boxes[$this->boxID], RequestHandler::getInstance()->getActiveRequest()->getRequestObject());
	}

	/**
	 * @see	\cms\system\content\IContent::getOutput()
	 */
	public function getOutput() {
		return $this->dashboardBox->getTemplate();
	}
}
