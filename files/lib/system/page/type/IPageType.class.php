<?php

namespace cms\system\page\type;
use cms\data\page\Page;
use wcf\form\AbstractForm;

/**
 * Interface for page types.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface IPageType {
	/**
	 * Returns whether it's currently possible to create a page of this
	 * type.
	 * 
	 * @return	boolean
	 */
	public function isAvailableToAdd();

	/**
	 * Reads page type specific parameters.
	 */
	public function readParameters(AbstractForm $form);

	/**
	 * Reads page type specific form parameters.
	 * 
	 * @return	array
	 */
	public function readFormParameters(AbstractForm $form);

	/**
	 * Reads page type specific data.
	 */
	public function readData(AbstractForm $form);

	/**
	 * Assigns page type specific variables.
	 */
	public function assignVariables(AbstractForm $form);

	/**
	 * Executes additional save methods.
	 */
	public function save(AbstractForm $form);

	/**
	 * Validates the submitted form data. In case of invalid inputs, throw
	 * an instance of '\wcf\system\exception\UserInputException'
	 * 
	 * @throws	\wcf\system\exception\UserInputException
	 */
	public function validate(AbstractForm $form);

	/**
	 * Returns the template name for the acp forms
	 * 
	 * @return	string
	 */
	public function getFormTemplate();

	/**
	 * Returns the template for the acp forms
	 * 
	 * @return	string
	 */
	public function getCompiledFormTemplate($assignValues = [], $errorField = '', $errorType = '');

	/**
	 * Returns the controller for the page type
	 * @example	(index.php?controller/alias/)
	 * 
	 * @return	string
	 */
	public function getController();
	
	/**
	 * Returns and array which will be used within the PageAction to
	 * update and create pages.
	 * 
	 * @return	array
	 */
	public function getSaveArray();
}
