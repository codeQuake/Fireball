<?php
namespace cms\system\content\form;
use cms\data\content\Content;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\util\StringUtil;

/**
 * Abstract content form implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentForm implements IContentForm {
	/**
	 * additional data
	 * @var	array<mixed>
	 */
	public $additionalData = array();

	/**
	 * environment
	 * @var	string
	 */
	public $environment = 'content';

	/**
	 * (temporary) identifier for this content
	 * @var	string
	 */
	public $identifier = '';

	/**
	 * database object action used for creating/editing the content
	 * @var	\cms\data\content\ContentAction
	 */
	public $objectAction = null;

	/**
	 * name of the object type
	 * @var	string
	 */
	protected static $objectTypeName = '';

	/**
	 * Id of the page the content will be assigned to. Notice that the page
	 * id is only available after the save method was called.
	 * @var	integer
	 */
	public $pageID = 0;

	/**
	 * show order of this content
	 * @var	integer
	 */
	public $showOrder = 0;

	/**
	 * Initializes a new instance of a content form. In case '$identifier'
	 * is not valid, doesn't belong to an existing content or the existing
	 * content is not of the right type, an exception is thrown.
	 * 
	 * @see	\cms\system\content\form\IContentForm::__construct()
	 */
	public function __construct($identifier) {
		$this->identifier = $identifier;

		// validate identifier
		if (!preg_match('~^(?:tmp_)?[0-9]+$~', $this->identifier)) {
			throw new SystemException('Invalid identifier given.');
		}
		if (intval($this->identifier)) {
			$content = new Content($this->identifier);
			if (!$content->contentID) {
				throw new SystemException("Given identifier doesn't belong to an existing content.");
			}

			$objectTypeID = $this->getObjectType()->objectTypeID;
			if ($objectTypeID != $content->objectTypeID) {
				throw new SystemException("Given object type and content's object type aren't identical.");
			}
		}

		// call 'construct' event
		EventHandler::getInstance()->fireAction($this, 'construct');
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::registerI18nInputs()
	 */
	public function registerI18nInputs() {
		// call 'registerI18nInputs' event
		EventHandler::getInstance()->fireAction($this, 'registerI18nInputs');
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::readFormParameters()
	 */
	public function readFormParameters() {
		// call 'readFormParameters' event
		EventHandler::getInstance()->fireAction($this, 'readFormParameters');

		// read general parameters
		if (isset($_POST['content_'.$this->identifier.'_showOrder'])) $this->showOrder = intval($_POST['content_'.$this->identifier.'_showOrder']);
		if (isset($_POST['content_'.$this->identifier.'_environment']) && in_array($_POST['content_'.$this->identifier.'_environment'], array('content', 'sidebar'))) $this->environment = $_POST['content_'.$this->identifier.'_environment'];
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::validate()
	 */
	public function validate() {
		// todo:
		// validate environment
		//if (!call_user_func(array($this->getObjectType()->getProcessor(), 'isAvailableIn'. StringUtil::firstCharToUpperCase($this->environment)))) {
		//	throw new UserInputException('content_'.$this->identifier.'_environment');
		//}

		// call 'validate' event
		EventHandler::getInstance()->fireAction($this, 'validate');
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::save()
	 */
	public function save($pageID) {
		$this->pageID = $pageID;

		// new content have a temporary identifier (prefix 'tmp_')
		// while existing contents keep their integer id as identifier
		if (mb_substr($this->identifier, 0, 4) == 'tmp_') {
			$this->create();
		} else {
			$this->update();
		}
	}

	/**
	 * Creates a database entry for this content.
	 */
	protected function create() {
		// call 'create' event
		EventHandler::getInstance()->fireAction($this, 'create');
	}

	/**
	 * Updates an existing database entry of this content.
	 */
	protected function update() {
		// call 'update' event
		EventHandler::getInstance()->fireAction($this, 'update');
	}

	/**
	 * Calls the 'saved' event after the successful call of one of the
	 * save methods. This function won't called automatically. You must do
	 * this manually.
	 */
	protected function saved() {
		// call 'saved' event
		EventHandler::getInstance()->fireAction($this, 'saved');
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::getFormVariables()
	 */
	public function getFormVariables() {
		return array_merge($this->additionalData, array(
			'environment' => $this->environment,
			'identifier' => $this->identifier
		));
	}

	/**
	 * Returns the object type this form is associated with.
	 * 
	 * @return	\wcf\data\object\type\ObjectType
	 */
	public function getObjectType() {
		return ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', static::$objectTypeName);
	}
}
