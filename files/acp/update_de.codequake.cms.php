<?php

use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\system\page\handler\PagePageHandler;
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
			'packageID' => $package->packageID,
			'applicationPackageID' => $package->packageID,
			'handler' => PagePageHandler::class,
			'controllerCustomURL' => $page->getAlias(),
			'lastUpdateTime' => $page->lastEditTime
		]
	]);
	$wcfPage = $pageAction->executeAction();

	$pageEditor = new PageEditor($page);
	$pageEditor->update(['wcfPageID' => $wcfPage['returnValues']->pageID]);
}
