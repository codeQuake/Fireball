<?php

namespace cms\system\page\type;
use wcf\form\AbstractForm;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Abstract page type implementation.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractPageType implements IPageType {
	/**
	 * controller of the frontend page
	 * @var string
	 */
	public $frontendController;

	/**
	 * this type of page can contain contents
	 * @var boolean
	 */
	public $canHaveContent = true;

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
	public $assignValues = [];
	
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
	 * @inheritDoc
	 */
	public function isAvailableToAdd() {
		return $this->isAvailable;
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @inheritDoc
	 */
	public function readData(AbstractForm $form) {
		if (empty($_POST)) {
			return [];
		} else {
			return $form->specificFormParameters;
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters(AbstractForm $form) {
		return [
			'data' => []
		];
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @inheritDoc
	 */
	public function save(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables(AbstractForm $form) { /* nothing */ }
	
	/**
	 * @inheritDoc
	 */
	public function getFormTemplate() {
		return $this->templateName;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getCompiledFormTemplate($assignValues = [], $errorField = '', $errorType = '') {
		if (empty($assignValues)) {
			$assignValues = array_merge_recursive($this->assignValues, [
				'errorField' => $errorField,
				'errorType' => $errorType
			]);
		} else {
			$assignValues = array_merge_recursive($assignValues, [
				'errorField' => $errorField,
				'errorType' => $errorType
			]);
		}
		return WCF::getTPL()->fetch($this->getFormTemplate(), 'cms', $assignValues);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getController() {
		return $this->frontendController;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSaveArray() {
		return [
			'data' => $this->assignValues
		];
	}
}
