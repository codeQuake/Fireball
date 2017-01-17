<?php

use cms\data\stylesheet\StylesheetEditor;
use cms\data\stylesheet\StylesheetList;

$stylesheetList = new StylesheetList();
$stylesheetList->readObjects();
$stylesheets = $stylesheetList->getObjects();

foreach ($stylesheets as $stylesheet) {
	$stylesheetEditor = new StylesheetEditor($stylesheet);
	$stylesheetEditor->update(['scss' => $stylesheet->less]);
}
