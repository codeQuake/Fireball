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

// install date
$sql = "UPDATE	wcf" . WCF_N . "_option
	SET	optionValue = ?
	WHERE	optionName = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array(
	TIME_NOW,
	'cms_install_date'
));

// set default page title
if (! defined('PAGE_TITLE') || ! PAGE_TITLE) {
	$sql = "UPDATE	wcf" . WCF_N . "_option
		SET	optionValue = ?
		WHERE	optionName = ?";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute(array(
		'Fireball CMS 2.0',
		'page_title'
	));
}
