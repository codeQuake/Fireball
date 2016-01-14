<?php
use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use wcf\data\object\type\ObjectTypeCache; 

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

$id = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.headline');
$list = new ContentList();
$list->getConditionBuilder()->add('content.position', array('sidebar'));
$list->getConditionBuilder()->add('content.contentTypeID', array($id));
$list->readObjects();

$affectedObjects = $list->getObjects();

//delete all headlines
$action = new ContentAction($affectedObjects, 'delete', array());
$action->executeAction();

$id = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.content.type', 'de.codequake.cms.content.type.template');
$list = new ContentList();
$list->getConditionBuilder()->add('content.contentTypeID', array($id));
$list->readObjects();
$affectedObjects = $list->getObjects();

// clear compiled
foreach ($affectedObjects as $content) {
	if (!empty($content->compiled)) {
		$contentData = $content->contentData;
		$tmpArray = unserialze($contentData);
		unset($tmpArray['compiled']);
		$contentData = serialze($tmpArray);
		
		$contentEditor = new ContentEditor($content);
		$contentEditor->update(array('contentData' => $contentData));
	}
}

