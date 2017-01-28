<?php

use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\data\stylesheet\StylesheetEditor;
use cms\data\stylesheet\StylesheetList;
use wcf\data\page\PageAction;

$package = $this->installation->getPackage();

$pageList = new PageList();
$pageList->readObjects();
$pages = $pageList->getObjects();
/** @var \cms\data\page\Page $page */
foreach ($pages as $page) {
	$pageAction = new PageAction([], 'create', [
		'data' => [
			'identifier' => 'de.codequake.cms.page' . $page->pageID,
			'name' => $page->getTitle(),
			'pageType' => 'system',
			'originIsSystem' => $package->packageID,
			'packageID' => $package->packageID,
			'applicationPackageID' => 0,
			'handler' => 'cms\\system\\page\\handler\\PagePageHandler',
			'controllerCustomURL' => $page->getAlias(),
			'lastUpdateTime' => $page->lastEditTime
		]
	]);
	$wcfPage = $pageAction->executeAction();

	$pageEditor = new PageEditor($page);
	$pageEditor->update(['wcfPageID' => $wcfPage['returnValues']->pageID]);
}
