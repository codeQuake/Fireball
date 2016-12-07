<?php
namespace cms\system\event\listener;

use wcf\data\package\PackageCache;
// use wcf\system\event\listener\IEventListener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\request\route\FireballRequestRoute;

/**
 * Sets costum menu item provider in order to manipulate menu link on runtime.
 * Only menu items linking to the controller 'cms\page\PagePage' are affected.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class RouteListener implements IParameterizedEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var $eventObj \wcf\system\request\RouteHandler */

		$route = new FireballRequestRoute();
		$eventObj->addRoute($route);
	}
}
