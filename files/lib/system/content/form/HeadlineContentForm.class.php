<?php
namespace cms\system\content\form;
use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;

/**
 * Handles inputs for a headline content.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class HeadlineContentForm extends AbstractContentForm {
	/**
	 * headline link
	 * @var	string
	 */
	public $link = '';

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::$objectTypeName
	 */
	protected static $objectTypeName = 'de.codequake.cms.content.type.headline';

	/**
	 * headline text
	 * @var	string
	 */
	public $text = '';

	/**
	 * headline type ('h1' - 'h5')
	 * @var	string
	 */
	public $type = 'h1';

	/**
	 * @see	\cms\system\content\form\IContentForm::registerI18nInputs()
	 */
	public function registerI18nInputs() {
		parent::registerI18nInputs();

		I18nHandler::getInstance()->register('content_'.$this->identifier.'_text');
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if ($_POST['content_'.$this->identifier.'_type']) $this->type = StringUtil::trim($_POST['content_'.$this->identifier.'_type']);
		if ($_POST['content_'.$this->identifier.'_link']) $this->link = StringUtil::trim($_POST['content_'.$this->identifier.'_link']);
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::validate()
	 */
	public function validate() {
		parent::validate();

		// type
		if (!preg_match('~^h[1-5]$~', $this->type)) {
			throw new UserInputException('content_'.$this->identifier.'_type');
		}

		// text
		if (!I18nHandler::getInstance()->validateValue('content_'.$this->identifier.'_text')) {
			if (I18nHandler::getInstance()->isPlainValue('content_'.$this->identifier.'_text')) {
				throw new UserInputException('content_'.$this->identifier.'_text');
			} else {
				throw new UserInputException('content_'.$this->identifier.'_text', 'multilingual');
			}
		}
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::create()
	 */
	protected function create() {
		parent::create();

		$this->additionalData = array_merge($this->additionalData, array(
			'link' => $this->link,
			'text' => $this->text,
			'type' => $this->type
		));

		$data = array(
			'pageID' => $this->pageID,
			'contentTypeID' => $this->getObjectType()->objectTypeID,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'contentData' => serialize($this->additionalData)
		);

		$this->objectAction = new ContentAction(array(), 'create', array('data' => $data));
		$returnValues = $this->objectAction->executeAction();

		$contentEditor = new ContentEditor($returnValues['returnValues']);
		$updateAdditionalData = array();

		// save multilingual inputs
		if (!I18nHandler::getInstance()->isPlainValue('content_'.$this->identifier.'_text')) {
			$updateAdditionalData['text'] = 'cms.content.text'.$contentEditor->contentID;
			I18nHandler::getInstance()->save('content_'.$this->identifier.'_text', $updateAdditionalData['text'], 'cms.content');
		}

		// save new information
		if (!empty($updateAdditionalData)) {
			$updateAdditionalData = array_merge($contentEditor->additionalData, $updateAdditionalData);
			$contentEditor->update(array('additionalData' => serialize($updateAdditionalData)));
		}

		$this->saved();
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::update()
	 */
	protected function update() {
		parent::update();

		// save text
		$languageVariable = 'cms.content.text'.$this->identifier;
		if (I18nHandler::getInstance()->isPlainValue('content_'.$this->identifier.'_text')) {
			I18nHandler::getInstance()->remove($languageVariable);
		} else {
			I18nHandler::getInstance()->save('content_'.$this->identifier.'_text', $languageVariable, 'cms.content');
			$this->text = $languageVariable;
		}

		$this->additionalData = array_merge($this->additionalData, array(
			'link' => $this->link,
			'text' => $this->text,
			'type' => $this->type
		));

		$data = array(
			'showOrder' => $this->showOrder,
			'contentData' => serialize($this->additionalData)
		);

		$this->objectAction = new ContentAction(array($this->identifier), 'update', array('data' => $data));
		$this->objectAction->executeAction();

		$this->saved();
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::getFormVariables()
	 */
	public function getFormVariables() {
		return array_merge(parent::getFormVariables(), array(
			'link' => $this->link,
			'text' => $this->text,
			'type' => $this->type
		));
	}
}
