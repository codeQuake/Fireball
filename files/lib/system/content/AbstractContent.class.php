<?php
namespace cms\system\content;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\event\EventHandler;
use wcf\system\WCF;

/**
 * Abstract implementation of a content.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 Florian Frantzen
 * @license	GNU General Public License <http://opensource.org/licenses/GPL-3.0>
 * @package	de.codequake.cms
 */
abstract class AbstractContent extends DatabaseObjectDecorator implements IContent {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'cms\data\content\Content';

	/**
	 * name of the template
	 * @var	string
	 */
	public $templateName = '';

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
	 * @see	\cms\system\content\IContent::readParameters()
	 */
	public function readParameters() {
		// call 'readParameters' event
		EventHandler::getInstance()->fireAction($this, 'readParameters');
	}

	/**
	 * @see	\cms\system\content\IContent::readData()
	 */
	public function readData() {
		// call 'readData' event
		EventHandler::getInstance()->fireAction($this, 'readData');
	}

	/**
	 * @see	\cms\system\content\IContent::getOutput()
	 */
	public function getOutput() {
		// call 'getOutput' event
		EventHandler::getInstance()->fireAction($this, 'getOutput');

		// assign variables
		WCF::getTPL()->assign(array(
			'content' => $this->getDecoratedObject()
		));

		return WCF::getTPL()->fetch($this->templateName, 'cms');
	}

	/**
	 * @see	\cms\system\content\IContent::setI18nOptions()
	 */
	public function setI18nOptions() {
		// call 'setI18nOptions' event
		EventHandler::getInstance()->fireAction($this, 'setI18nOptions');
	}
}
