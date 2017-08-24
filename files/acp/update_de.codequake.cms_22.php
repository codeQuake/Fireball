<?php

use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\file\FileEditor;
use cms\data\file\FileList;
use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\data\stylesheet\StylesheetEditor;
use cms\data\stylesheet\StylesheetList;
use cms\system\page\handler\PagePageHandler;
use wcf\data\page\PageAction;
use wcf\system\language\LanguageFactory;

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
	if ($typeName != 'de.codequake.cms.content.type.headline' || $typeName != 'de.codequake.cms.content.type.box'
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
	$parentPage = $page->getParentPage();

	$availableLanguages = LanguageFactory::getInstance()->getLanguages();
	$contents = [];
	foreach ($availableLanguages as $language) {
		$contents[$language->languageID] = [
			'title' => $language->get($page->title),
			'content' => '',
			'metaDescription' => $language->get($page->metaDescription),
			'metaKeywords' => $language->get($page->metaKeywords),
			'customURL' => ''
		];
	}

	$pageAction = new PageAction([], 'create', [
		'data' => [
			'identifier' => 'de.codequake.cms.page' . $page->pageID,
			'name' => $page->getTitle(),
			'pageType' => 'system',
			'packageID' => $package->packageID,
			'applicationPackageID' => $package->packageID,
			'handler' => PagePageHandler::class,
			'controllerCustomURL' => $page->getAlias(),
			'lastUpdateTime' => $page->getLastEditTime(),
			'parentPageID' => $parentPage === null ? null : $parentPage->wcfPageID
		],
		'content' => $contents
	]);
	$wcfPage = $pageAction->executeAction();

	$pageEditor = new PageEditor($page);
	$pageEditor->update(['wcfPageID' => $wcfPage['returnValues']->pageID]);
}

$fileList = new FileList();
$fileList->readObjects();
$files = $fileList->getObjects();
/** @var \cms\data\file\File $file */
foreach ($files as $file) {
	if ($file->filename) {
		continue;
	}
	
	$fileEditor = new FileEditor($file);
	$fileEditor->update([
		'filename' => $file->title
	]);
}
