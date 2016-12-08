<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Abstract content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentType implements IContentType {
	/**
	 * name of the icon to display
	 * @var	string
	 */
	protected $icon = 'fa-unchecked';

	/**
	 * list of multilingual fields
	 * @var	array<string>
	 */
	public $multilingualFields = array();

	/**
	 * list of preview fields
	 * @var array<string>
	 */
	protected $previewFields = array();
	
	/**
	 * content requires title
	 * @var boolean
	 */
	public $requiresTitle = false;
	
	/**
	 * template name
	 * @var	string
	 */
	public $templateName = '';

	/**
	 * Initialize a new content type instance
	 */
	public function __construct() {
		// try to guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}
	}

	/**
	 * @see \cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		WCF::getTPL()->assign(array(
			'content' => $content
		));

		return WCF::getTPL()->fetch($this->templateName, 'cms');
	}

	/**
	 * @see \cms\system\content\type\IContentType::getIcon()
	 */
	public function getIcon() {
		return $this->icon;
	}
	
	/**
	 * @see \cms\system\content\type\IContentType::getPreview()
	 */
	public function getPreview(Content $content) {
		if (!empty($this->previewFields)) {
			$preview = '';
			foreach ($this->previewFields as $field) {
				if ((string) $content->{$field} != '') {
					$preview .= ' - ';
					$preview .= strip_tags($content->{$field});
				}
			}
			return StringUtil::truncate(substr($preview, 3), 70);
		}
		else {
			//no fields given, return ID
			return 'Content #'.$content->contentID;
		}
	}

	/**
	 * @see	\cms\system\content\type\IContentType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		return true;
	}

	/**
	 * @see	\cms\system\content\type\IContentType::readParameters()
	 */
	public function readParameters() {
		// register multilingual fields
		foreach ($this->multilingualFields as $field) {
			I18nHandler::getInstance()->register($field);
		}
	}

	/**
	 * @see	\cms\system\content\type\IContentType::readFormParameters()
	 */
	public function readFormParameters() { /* nothing */ }

	/**
	 * @see cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) { /* nothing */ }

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		return $this->templateName;
	}
}
