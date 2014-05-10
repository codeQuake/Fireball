<?php

namespace cms\system\content\type;
use cms\data\content\Content;

/**
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
class AbstractContentType implements IContentType {


	protected $icon = 'icon-unchecked';
	public $objectType = '';
	public $isMultilingual = false;
	public $multilingualFields = array();

	/**
	 *
	 * @see cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) { }

	/**
	 *
	 * @see \cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		return '';
	}

	/**
	 *
	 * @see \cms\system\content\type\IContentType::getIcon()
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 *
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		return '';
	}
}
