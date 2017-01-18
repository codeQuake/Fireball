<?php
namespace wcf\system\event\listener;

use cms\system\request\PageRoute;
use wcf\system\event\listener\IParameterizedEventListener;

/**
 * Registers all cms specific routes.
 * Listener has to be placed within wcf namespace because application wouldn't
 * be uninstallable otherwise.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSRouteHandlerListener implements IParameterizedEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// only register routes when an application is active
		if (PACKAGE_ID !== 1) {
			try {
				$route = new PageRoute();
				$eventObj->addRoute($route);
			}
			catch (\TypeError $e) {
				// workaround during upgrade to wsc 3.0
			}
		}
	}
}
