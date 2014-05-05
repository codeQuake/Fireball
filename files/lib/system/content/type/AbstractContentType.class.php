<?php
namespace cms\system\content\type;

class AbstractContentType implements IContentType{

	/**
	 * @see cms\system\content\type\IContentType::$icon
	 */
	protected static $icon = 'icon-unchecked';

	/**
	 * @see cms\system\content\type\IContentType::$objectType
	 */
	public $objectType = '';

	/**
	 * @see cms\system\content\type\IContentType::validate()
	 */
	public function validate(){ /** EMPTY **/ }

	/**
	 * @see \cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(){
		return '';
	}

	/**
	 * @see \cms\system\content\type\IContentType::getIcon()
	 */
	public function getIcon(){
		return self::$icon;
	}

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate(){
		return '';
	}
}