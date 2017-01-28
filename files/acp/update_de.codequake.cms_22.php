<?php

use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\data\stylesheet\StylesheetEditor;
use cms\data\stylesheet\StylesheetList;
use wcf\data\page\PageAction;

$package = $this->installation->getPackage();

$stylesheetList = new StylesheetList();
$stylesheetList->readObjects();
$stylesheets = $stylesheetList->getObjects();

/** @var \cms\data\stylesheet\Stylesheet $stylesheet */
foreach ($stylesheets as $stylesheet) {
	$stylesheetEditor = new StylesheetEditor($stylesheet);
	$stylesheetEditor->update(['scss' => $stylesheet->less]);
}

$contentList = new ContentList();
$contentList->readObjects();
$contents = $contentList->getObjects();
/** @var \cms\data\content\Content $content */
foreach ($contents as $content) {
	$typeName = $content->getTypeName();
	if ($typeName != 'de.codequake.cms.content.type.headline' || $typeName != 'de.codequake.cms.content.type.dashboard'
		|| $typeName != 'de.codequake.cms.content.type.user' || $typeName != 'de.codequake.cms.content.type.group'
		|| $typeName != 'de.codequake.cms.content.type.tabmenu' || $typeName != 'de.codequake.cms.content.type.columns'
		|| $typeName != 'de.codequake.cms.content.type.wsipimport') {
		continue;
	}

	$contentEditor = new ContentEditor($content);
	$contentEditor->update(['showHeadline' => 1]);
}

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
