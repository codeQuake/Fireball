<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DashboardContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-dashboard';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		WCF::getTPL()->assign(array(
			'boxList' => DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes')
		));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$boxes = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
		if (!isset($boxes[$content->box])) {
			// dashboard box doesn't exist anymore
			return '';
		}

		$className = $boxes[$content->box]->className;
		$box = new $className();
		$box->init($boxes[$content->box], RequestHandler::getInstance()->getActiveRequest()->getRequestObject());

		return $box->getTemplate();
	}
}
