<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\page\PagePage;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
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
		$boxID = $content->box;
		$boxList = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
		$className = $boxList[$boxID]->className;
		$box = new $className();
		$box->init($boxList[$boxID], new PagePage());

		return $box->getTemplate();
	}
}
