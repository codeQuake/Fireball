<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\language\I18nHandler;

/**
 * Abstract content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentType implements IContentType {
	/**
	 * name of the icon to display
	 * @var	string
	 */
	protected $icon = 'icon-unchecked';

	/**
	 * list of multilingual fields
	 * @var	array<string>
	 */
	public $multilingualFields = array();

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
		return '';
	}

	/**
	 * @see \cms\system\content\type\IContentType::getIcon()
	 */
	public function getIcon() {
		return $this->icon;
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
