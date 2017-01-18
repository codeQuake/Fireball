<?php

use cms\data\content\ContentEditor;
use cms\data\content\ContentList;
use cms\data\stylesheet\StylesheetEditor;
use cms\data\stylesheet\StylesheetList;

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
