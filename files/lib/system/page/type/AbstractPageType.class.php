<?php

namespace cms\system\page\type;
use wcf\form\AbstractForm;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Abstract page type implementation.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractPageType implements IPageType {
	/**
	 * controller of the frontend page
	 * @var Class
	 */
	public $frontendController;

	/**
	 * template name
	 * @var	string
	 */
	public $templateName = '';
	
	/**
	 * page type is available to be added
	 * @var boolean
	 */
	public $isAvailable = true;
	
	/**
	 * array with specific form data
	 * @var array
	 */
	public $assignValues = array();
	
	/**
	 * Initialize a new page type instance
	 */
	public function __construct() {
		// try to guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}

		if ($this->frontendController === null)
			throw new SystemException('Page type "' . get_class($this) . '" does not provide a valid frontend controller.');
	}
	
	/**
	 * @see	\cms\system\page\type\IPageType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		return $this->isAvailable;
	}
	
	/**
	 * @see	\cms\system\page\type\IPageType::readParameters()
	 */
	public function readParameters(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @see \cms\system\page\type\IPageType::readData()
	 */
	public function readData(AbstractForm $form) {
		if (empty($_POST)) {
			if (!empty($form->page))
				$page = $form->page;
			
			$return = array();
			
			return $return;
		} else {
			return $form->specificFormParameters;
		}
	}
	
	/**
	 * @see	\cms\system\page\type\IPageType::readFormParameters()
	 */
	public function readFormParameters(AbstractForm $form) {
		return array (
			'data' => array()
		);
	}
	
	/**
	 * @see \cms\system\page\type\IPageType::validate()
	 */
	public function validate(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @see \cms\system\page\type\IPageType::save()
	 */
	public function save(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @see \cms\system\page\type\IPageType::assignVariables()
	 */
	public function assignVariables(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @see \cms\system\page\type\IPageType::getFormTemplate()
	 */
	public function getFormTemplate() {
		return $this->templateName;
	}
	
	/**
	 * @see \cms\system\page\type\IPageType::getCompiledFormTemplate()
	 */
	public function getCompiledFormTemplate($assignValues = array(), $errorField = '', $errorType = '') {
		if (empty($assignValues)) {
			$assignValues = array_merge_recursive($this->assignValues, array (
				'errorField' => $errorField,
				'errorType' => $errorType
			));
		} else {
			$assignValues = array_merge_recursive($assignValues, array (
				'errorField' => $errorField,
				'errorType' => $errorType
			));
		}
		return WCF::getTPL()->fetch($this->getFormTemplate(), 'cms', $assignValues);
	}
	
	/**
	 * @see \cms\system\page\type\IPageType::getController()
	 */
	public function getController() {
		return $this->frontendController;
	}
	
	/**
	 * @see \cms\system\page\type\IPageType::getSaveArray()
	 */
	public function getSaveArray() {
		return array (
			'data' => $this->assignValues
		);
	}
}
