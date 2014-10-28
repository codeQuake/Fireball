<?php
namespace cms\system\content\type;
use wcf\data\object\type\AbstractObjectTypeProcessor;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Abstract content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentType extends AbstractObjectTypeProcessor implements IContentType {
	/**
	 * name of the content form class
	 * @var	string
	 */
	protected static $contentFormClass = '';

	/**
	 * name of the content processor class
	 * @var	string
	 */
	protected static $contentProcessorClass = '';

	/**
	 * name of the icon to display
	 * @var	string
	 */
	protected $icon = 'icon-unchecked';

	/**
	 * name of the template
	 * @var	string
	 */
	protected $templateName = '';

	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::__construct()
	 */
	public function __construct(DatabaseObject $object) {
		parent::__construct($object);

		// try to guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getContentFormClass()
	 */
	public static function getContentFormClass() {
		// guess content form class
		if (empty(static::$contentFormClass)) {
			$classParts = explode('\\', get_called_class());
			$className = substr(array_pop($classParts), 0, -4) . 'Form';

			// remove 'type' namespace part
			array_pop($classParts);

			static::$contentFormClass = implode('\\', $classParts) . '\\form\\' . $className; 
		}

		return static::$contentFormClass;
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getContentProcessorClass()
	 */
	public static function getContentProcessorClass() {
		// guess content processor class
		if (empty(static::$contentProcessorClass)) {
			$classParts = explode('\\', get_called_class());
			$className = substr(array_pop($classParts), 0, -4);

			// remove 'type' namespace part
			array_pop($classParts);

			static::$contentProcessorClass = implode('\\', $classParts) . '\\' . $className;
		}

		return static::$contentProcessorClass;
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getFormOutput()
	 */
	public function getFormOutput() {
		return WCF::getTPL()->fetch($this->templateName, 'cms');
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getIcon()
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
}
