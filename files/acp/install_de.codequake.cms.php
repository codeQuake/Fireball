<?php
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
$sql = "UPDATE	wcf".WCF_N."_option
	SET	optionValue = ?
	WHERE	optionName = ?";
$optionUpdate = WCF::getDB()->prepareStatement($sql);

// install date
$optionUpdate->execute(array(TIME_NOW, 'cms_install_date'));

// set default page title
if (!defined('PAGE_TITLE') || !PAGE_TITLE) {
	$optionUpdate->execute(array('Fireball CMS 2.0', 'page_title'));
}

// create default file category
CategoryEditor::create(array(
	'objectTypeID' => ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.category', 'de.codequake.cms.file'),
	'title' => 'Default Category',
	'time' => TIME_NOW
));
