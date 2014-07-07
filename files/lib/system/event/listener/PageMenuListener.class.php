<?php
namespace cms\system\event\listener;

use wcf\data\package\PackageCache;
use wcf\system\event\IEventListener;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */

class PageMenuListener implements IEventListener {

	public function execute($eventObj, $className, $eventName) {
		if ($eventObj->menuItemController == 'cms\page\PagePage') {
			$eventObj->additionalFields['className'] = 'cms\system\menu\page\CMSPageMenuItemProvider';
			$eventObj->additionalFields['packageID'] = PackageCache::getInstance()->getPackageID('de.codequake.cms');
		}
	}
}
