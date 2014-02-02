<?php
use cms\data\stylesheet\StylesheetAction;
use cms\data\module\ModuleAction;


//install css templates

//two columns
$twoColumnStyle = "@media only screen and (min-width: 801px){
.col-2{ float: left; padding: 10px; width: 50%; box-sizing: border-box;}
	
}
.clear {clear:both;}";

$data = array('title' => 'Two Colums / Zweispaltig',
              'less' => $twoColumnStyle);
$objectAction = new StylesheetAction(array(), 'create', array('data' => $data));
$objectAction->executeAction();

//three columns
$threeColumnStyle = "@media only screen and (min-width: 801px){
.col-3{ float: left; padding: 10px; width: 33%; box-sizing: border-box;}
	
}
.clear {clear:both;}";

$data = array('title' => 'Three Colums / Dreispaltig',
              'less' => $threeColumnStyle);
$objectAction = new StylesheetAction(array(), 'create', array('data' => $data));
$objectAction->executeAction();

//four columns
$fourColumnStyle = "@media only screen and (min-width: 801px){
.col-4{ float: left; padding: 10px; width: 25%; box-sizing: border-box;}
	
}
.clear {clear:both;}";

$data = array('title' => 'Four Colums / Vierspaltig',
              'less' => $fourColumnStyle);
$objectAction = new StylesheetAction(array(), 'create', array('data' => $data));
$objectAction->executeAction();


//install modules

//JS Slider
$sliderJS = "<!--Um dieses Modul zu benutzen, geben Sie einem Abschnitt (Liste wie z.B Bilder/Nachrichten) die Klasse 'slideshowContainer'-->
             <!--To use this module please use the css class 'slideshowContainer' in your section (list like images/news)-->
        <script data-relocate=\"true\">
		//<![CDATA[
		$(function() {
		$('.slideshowContainer').wcfSlideshow();
		});
		//]]>
	</script>";
$data = array('data' => array('moduleTitle' => 'sliderJS'),
                      'source' => array(
                                      'php' => '',
                                      'tpl' => $sliderJS));
$action  = new ModuleAction(array(), 'create', $data);
$action->executeAction();
