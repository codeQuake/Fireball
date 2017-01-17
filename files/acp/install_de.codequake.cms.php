<?php
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\package\update\server\PackageUpdateServerAction;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
$sql = "UPDATE	wcf".WCF_N."_option
	SET	optionValue = ?
	WHERE	optionName = ?";
$optionUpdate = WCF::getDB()->prepareStatement($sql);

// set default page title
if (!defined('PAGE_TITLE') || !PAGE_TITLE) {
	$optionUpdate->execute(['Fireball CMS 3.0', 'page_title']);
}

// create default file category
CategoryEditor::create([
	'objectTypeID' => ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.category', 'de.codequake.cms.file'),
	'title' => 'Default Category',
	'time' => TIME_NOW
]);

// add codequake update server
if (isset($this->instruction['attributes']['installupdateserver']) && $this->instruction['attributes']['installupdateserver'] == 1) {
	$serverURL = 'https://update.mysterycode.de/vortex/';

	// check if update server already exists
	$sql = "SELECT	packageUpdateServerID
		FROM	wcf".WCF_N."_package_update_server
		WHERE	serverURL = ?";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute([$serverURL]);
	$row = $statement->fetchArray();
	if ($row === false) {
		$objectAction = new PackageUpdateServerAction([], 'create', [
			'data' => [
			'serverURL' => $serverURL
			]
		]);
		$objectAction->executeAction();
	}
}
