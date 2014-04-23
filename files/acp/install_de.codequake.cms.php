<?php
use cms\data\module\ModuleAction;
use cms\data\stylesheet\StylesheetAction;
use wcf\system\dashboard\DashboardHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
$package = $this->installation->getPackage();

// default values
DashboardHandler::setDefaultValues('de.codequake.cms.news.newsList', array(
	'de.codequake.cms.latestNews' => 1
));

// install date
$sql = "UPDATE	wcf" . WCF_N . "_option
    SET	optionValue = ?
    WHERE	optionName = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(
	TIME_NOW,
	'cms_install_date'
));
