<?php
namespace cms\system\content\type;
use cms\data\content\Content;

class AbstractStructureContentType extends AbstractContentType {

	//gets structure css classes for building css
	public function getCSSClasses() {
		return '';
	}

	public function getChildCSSClasses(Content $content) {
		return '';
	}
}
