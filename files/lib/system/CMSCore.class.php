<?php
namespace cms\system;
use wcf\system\application\AbstractApplication;
use wcf\system\cache\CacheHandler;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Fireball core.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
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
