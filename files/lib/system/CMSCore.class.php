<?php
namespace cms\system;

use cms\data\page\Page;
use cms\data\page\PageCache;
use cms\system\menu\page\CMSPageMenuItemProvider;
use wcf\system\application\AbstractApplication;
use wcf\system\menu\page\PageMenu;
use wcf\system\WCF;

/**
 * Fireball core.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSCore extends AbstractApplication {
	/**
	 * @see \wcf\system\application\AbstractApplication::$primaryController
	 */
	protected $primaryController = 'cms\page\PagePage';
}
