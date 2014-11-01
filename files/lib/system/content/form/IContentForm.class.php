<?php
namespace cms\system\content\form;

/**
 * Content Form classes handle the acp input for contents of their type. Do
 * notice that one instance of the form class handles inputs for exactly one
 * content. If you have, for example, ten contents on one page, ten form class
 * instances would be created, no matter of which type they are.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface IContentForm {
	/**
	 * Initializes a new instance of a content form. '$identifier' provides
	 * an unique identifier to identify contents during a page edit
	 * session. The value may equals the content id for existing contents.
	 * Otherwise, it's a consecutive numbering of all contents prefixed
	 * with 'tmp_'.
	 * 
	 * Security notice: Fireball CMS directly passes the identifier from
	 * the client without further validation. You *must* validate that the
	 * identifier is an integer, optionally prefixed with 'tmp_'. Moreover,
	 * when the identifier is not prefixed and therefore the id of an
	 * existing content, you *have to* verify that a content with that id
	 * exists and that the content is of the right type.
	 * 
	 * @param	string		$identifier
	 */
	public function __construct($identifier);

	/**
	 * This method is called just before the i18n-handler reads
	 * multilingual values. Use this method to register i18n-inputs.
	 */
	public function registerI18nInputs();

	/**
	 * Reads the form parameters for this content. Notice that this method
	 * is called after the i18n-handler has read all multilingual values.
	 */
	public function readFormParameters();

	/**
	 * Validates the form input for this content. In case of invalid inputs,
	 * throw an instance of '\wcf\system\exception\UserInputException'.
	 */
	public function validate();

	/**
	 * Saves the content. Notice that this method needs to handle both
	 * creating new contents and updating existing ones. In case the
	 * identifier passed to the constructer is prefixed with 'tmp_', you
	 * need to create a new content, otherwise update the content with the
	 * given id.
	 * 
	 * @param	integer		$pageID
	 */
	public function save($pageID);

	/**
	 * Returns an array of all form variables for this content. Required
	 * variables are 'identifier' and 'environment'. Other variables may be
	 * needed by the specific content type.
	 * 
	 * @return	array
	 */
	public function getFormVariables();
}
