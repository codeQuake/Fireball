<?php

use cms\data\page\PageEditor;
use cms\data\page\PageList;
use cms\system\page\handler\PagePageHandler;
use wcf\data\page\PageAction;
use wcf\system\language\LanguageFactory;

$package = $this->installation->getPackage();

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
			'lastUpdateTime' => $page->getLastEditTime(),
			'parentPageID' => $parentPage === null ? null : $parentPage->wcfPageID
		],
		'content' => $contents
	]);
	$wcfPage = $pageAction->executeAction();

	$pageEditor = new PageEditor($page);
	$pageEditor->update(['wcfPageID' => $wcfPage['returnValues']->pageID]);
}
