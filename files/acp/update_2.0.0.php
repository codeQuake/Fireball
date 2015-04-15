<?php
use cms\data\page\revision\PageRevisionEditor;
use cms\data\page\revision\PageRevisionList;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

//update revisions - taken from wcf2.1 branch files/acp/update_2.0.php 
$revisionList = new PageRevisionList();
$revisionList->readObjects();
foreach ($revisionList->getObjects() as $revision) {
	$teststring = $revision->data;
	$teststring = base64_decode($teststring);
	
	if (@unserialize($teststring)) {
		$updateData = array(
			'data' => base64_encode($revision->data),
			'contentData' => base64_encode($revision->contentData)
		);
		
		$revisionEditor = new PageRevisionEditor($revision);
		$revisionEditor->update($updateData);
	}
}
