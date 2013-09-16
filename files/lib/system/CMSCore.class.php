<?php
namespace cms\system;
use wcf\system\cache\CacheHandler;
use wcf\system\menu\page\PageMenu;
use wcf\system\application\AbstractApplication;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * FireBall core.
 *
 * @author	Jens Krumsieck
 * @copyright	2013 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */
class CMSCore extends AbstractApplication {
	/**
	 * @see	AbstractApplication::$abbreviation
	 */
	protected $abbreviation = 'cms';
	/**
	 * @see wcf\system\application\AbstractApplication
	 */
	public function __run() {
		if (!$this->isActiveApplication()) {
			return;
		}
		
		PageMenu::getInstance()->setActiveMenuItem('cms.pageMenu.index');
	}
}
