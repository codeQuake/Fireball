<?php
namespace cms\system\content\form;
use cms\data\content\ContentAction;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles inputs for a template content.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TemplateContentForm extends AbstractContentForm {
	/**
	 * text
	 * @var	string
	 */
	public $text = '';

	/**
	 * @see	\cms\system\content\form\IContentForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if ($_POST['content_'.$this->identifier.'_text']) $this->text = StringUtil::trim($_POST['content_'.$this->identifier.'_text']);
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::validate()
	 */
	public function validate() {
		parent::validate();

		// text
		if (empty($this->text)) {
			throw new UserInputException('content_'.$this->identifier.'_text');
		}

		try {
			WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template', $this->text);
		}
		catch (SystemException $e) {
			WCF::getTPL()->assign(array(
				'additionalErrorData' => $e->_getMessage()
			));

			throw new UserInputException('content_'.$this->identifier.'_text', 'compileError');
		}
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::create()
	 */
	protected function create() {
		parent::create();

		$this->additionalData = array_merge($this->additionalData, array(
			'text' => $this->text
		));

		$data = array(
			'pageID' => $this->pageID,
			'contentTypeID' => $this->getObjectType()->objectTypeID,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'contentData' => serialize($this->additionalData)
		);

		$this->objectAction = new ContentAction(array(), 'create', array('data' => $data));
		$this->objectAction->executeAction();

		$this->saved();
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::update()
	 */
	protected function update() {
		parent::update();

		$this->additionalData = array_merge($this->additionalData, array(
			'text' => $this->text
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
			'text' => $this->text
		));
	}
}
