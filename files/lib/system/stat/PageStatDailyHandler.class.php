<?php
namespace cms\system\stat;

use wcf\system\stat\AbstractStatDailyHandler;

/**
 * Stat handler implementation for pages.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 Florian Frantzen
 * @license	GNU General Public License <http://opensource.org/licenses/GPL-3.0>
 * @package	de.codequake.cms
 */
class PageStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @see	\wcf\system\stat\IStatDailyHandler::getData()
	 */
	public function getData($date) {
		return [
			'counter' => $this->getCounter($date, 'cms'.WCF_N.'_page', 'creationTime'),
			'total' => $this->getTotal($date, 'cms'.WCF_N.'_page', 'creationTime')
		];
	}
}
